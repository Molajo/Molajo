<?php

/**
 * class which converts a mysql workbench file to a simplified schema xml
 * @author Thomas Schäfer <thomas.schaefer@query4u.de>
 * @throws Exception
 */
class MySQLWorkbench {

  const MODE_SOURCE = 1;
  const MODE_SCHEMA = 2;
  
  protected $debug = 0;
  protected $header = true;
  protected $error;
  protected $mwb;
  protected $xmlstream;
  protected $xsl;
  
  /**
   * @var DomDocument $reader
   */
  protected $reader;
  /**
   * @var DomDocument $writer
   */
  protected $writer;

  /**
   * @var DomXPath $query
   */
  protected $query;

  /**
   * @var DomNodeList $filter
   */
  protected $filter;

  /**
   * @param string $name mwb file name
   * @param string $path to mbw file
   * @throws Exception 
   */
  public function __construct($name, $path) {
    ini_set("max_execution_time", 600);
      
    try
    {
      if(!class_exists("ZipArchive")) {
        throw new Exception(get_class($this) . " needs class ZipArchive.");
      }
      if(!class_exists("DomXPath")) {
        throw new Exception(get_class($this) . " needs class DomXPath.");
      }
      if(!class_exists("DomDocument")) {
        throw new Exception(get_class($this) . " needs class DoomDocument.");
      }
      
      if (is_dir($path))
      {
        $this->mwb = sprintf("%s/%s.mwb", $path, strtolower($name));
        if (!is_file($this->mwb))
        {
          throw new Exception("expecting a file named " . $this->mwb);
        }
      }
      else
      {
        throw new Exception("expecting a valid directory path: " . $path);
      }
      $this->xsl = dirname(__FILE__). "/mwb.xsl";

      libxml_use_internal_errors(true);
      
    }
    catch (Exception $e)
    {
      $this->error = $e->getMessage();
    }
  }
  
  protected function XML() {
    $xml = new DOMDocument("1.0", "utf-8");
    $xml->preserveWhiteSpace = false;
    return $xml;
  }
  
  public function setDebugMode($int) {
    $this->debug = $int;
    return $this;
  }

  public function run() {
    $this->init();

    $this->Writer();

    if ($this->debug === 1)
    {
      header("content-type:text/xml");
      die($this->xmlstream);
    }

    $this->filterTables();
    $xmlstring = $this->GetWriter()->saveXML();

    if ($this->debug === 2)
    {
      header("content-type:text/xml");
      die($xmlstring);
    }

    $xml = new MySQLWorkbenchXML();
    $xml->loadXML($xmlstring);
    $xml->XSL($this->xsl);
  
    $this->result = $xml;

    return $this;
  }
  
  protected function hasError() {
    return isset($this->error);
  }

  public function filterTables() {
    $this->filter = $this->query->query(sprintf("//value[@struct-name='db.mysql.Table']"));
    if ($this->filter instanceof DOMNodeList and $this->filter->length > 0)
    {
      $f = $this->filter;

      for ($i = 0; $i < $f->length; $i++)
      {
        $owner = $f->item($i)->getAttribute("id");

        $x = new DOMXPath($this->reader);
        $res = $x->query("//value[@id='$owner']/value[@key='columns']");

        $y = new DOMXPath($this->reader);
        $fks = $y->query("//value[@id='$owner']/value[@key='foreignKeys']");

        $node = $f->item($i);
        $node->setAttribute("key", "tables");

        if ($node)
        {
          $node = $f->item($i);
          $cols = $this->writer->createElement("value");
          $cols->setAttribute("key", "columns");
          for ($j = 0; $j < $res->length; $j++)
          {
            $cols->appendChild($this->writer->importNode($res->item($j), true));
          }

          $this->writer->documentElement->appendChild(
            $this->writer->importNode($node, true)
          );

          $this->writer->documentElement->childNodes->item($i)->appendChild($cols);

          $fk = $this->writer->createElement("value");
          $fk->setAttribute("key", "foreignkeys");
          for ($j = 0; $j < $fks->length; $j++)
          {
            $fk->appendChild($this->writer->importNode($fks->item($j), true));
          }
          $this->writer->documentElement->childNodes->item($i)->appendChild($fk);

        }
      }

      $res = $this->query->query("//value[@key='catalog']/value[@key='schemata']/value[@struct-name='db.mysql.Schema']/value[@key='comment']/text()");
      if ($res->length > 0)
      {
        $json = preg_replace('/([{,])(\s*)([^"]+?)\s*:/', '$1"$3":', trim((string)$res->item(0)->nodeValue));
        $obj = json_decode($json, true);

        $control = $this->writer->createElement("application");
        $control->setAttribute("key", "install");
        foreach ($obj as $key => $val)
        {
          $control->setAttribute($key, $val);
        }
        $this->writer->documentElement->lastChild->appendChild($control);
      }

    }
    return $this;
  }


  public function init() {
    if ($this->hasError())
    {
      return $this->error;
    }
    $this->getFromStream();
    $this->schema2DOM();
    return $this;
  }

  /**
   * @return DomDocument
   */
  public function Reader() {
    if ($this->header and $this->reader instanceof DOMDocument)
    {
      header("content-type: text/xml");
    }
    return $this->reader;
  }

  public function Writer() {
    $this->writer = new DOMDocument("1.0", "utf-8");

    $data = $this->writer->createElement("data");

    $o = new DOMXPath($this->reader);

    $res = $o->query("//data/value/value[@key='info']/value[@key='caption']");
    if ($res->length > 0)
      $data->setAttribute("name", $res->item(0)->nodeValue);

    $res = $o->query("//data/value/value[@key='info']/value[@key='project']");
    if ($res->length > 0)
      $data->setAttribute("project", $res->item(0)->nodeValue);

    $res = $o->query("//data/value/value[@key='info']/value[@key='dateChanged']");
    if ($res->length > 0)
      $data->setAttribute("modifiedAt", $res->item(0)->nodeValue);

    $res = $o->query("//data/value/value[@key='info']/value[@key='dateCreated']");
    if ($res->length > 0)
      $data->setAttribute("createdAt", $res->item(0)->nodeValue);

    $res = $o->query("//value[@key='catalog']/value[@key='schemata']/value[@struct-name='db.mysql.Schema']/value[@key='name']");
    if ($res->length > 0)
      $data->setAttribute("schema", $res->item(0)->nodeValue);

    $this->writer->appendChild($data);

    return $this;
  }

  public function GetWriter() {
    if ($this->header and $this->writer instanceof DomDocument)
    {
      header("content-type: text/xml");
    }
    return $this->writer;
  }

  public function noheader() {
    $this->header = false;
    return $this;
  }

  /**
   * @var MySQLWorkbenchXML $result
   */
  protected $result;

  public function render() {
    return $this->result->transform();
  }

  public function execute() {
    $this->init();
  }

  protected function schema2DOM() {
    $dom = new DOMDocument("1.0", "utf-8");
    $dom->loadXML($this->xmlstream);
    $this->reader = $dom;
    $this->query = new DOMXPath($this->reader);
  }

  protected function getFromStream() {
    try
    {
      $za = new ZipArchive();
      if ($za->open($this->mwb))
      {
        if (0 < $za->numFiles)
        {
          $stat = $za->statIndex(0);
          $filename = $stat["name"];

          if (basename($filename) == "document.mwb.xml")
          {
            $fp = $za->getStream($filename);
            if (!$fp)
            {
              throw new Exception("Error: can't get stream to zipped file");
            }
            else
            {
              $stat = $za->statName($stat["name"]);

              $buf = "";
              ob_start();
              while (!feof($fp))
              {
                $buf .= fread($fp, 2048);
              }
              $xmlstream = ob_get_contents();
              ob_end_clean();

              if (stripos($xmlstream, "CRC error") != FALSE)
              {
                $errBuff = '';
                ob_start();
                echo 'CRC32 mismatch, current ';
                printf("%08X", crc32($buf)); //current CRC
                echo ', expected ';
                printf("%08X", $stat['crc']); //expected CRC
                $error = ob_get_contents();
                ob_end_clean();
                fclose($fp);

                throw new Exception($error);
              }
              fclose($fp);

              $this->xmlstream = $buf;

            }
          }
          else
          {
            throw new Exception("archive has to contain document.mwb.xml as first entry document.");
          }
        }
        else
        {
          $za->close();
          throw new Exception("zip archive is empty");
        }
      }
      else
      {
        throw new Exception("could not open archive " . $this->mwb);
      }
    }
    catch (Exception $e)
    {
      $this->error = $e->getMessage();
    }

  }


}