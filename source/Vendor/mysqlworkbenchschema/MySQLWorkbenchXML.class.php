<?php
/**
 * xml helper class
 * @author Thomas Schäfer <thomas.schaefer@query4u.de>
 */
class MySQLWorkbenchXML {

  /**
   * @var DOMDocument $xml
   */
  private $xml;
  /**
   * @var XSLTProcessor $xsl
   */
  private $xsl;

  /**
   * @var string $schema
   */
  private $schema;

  /**
   * @var DomDocument $errors
   */
  private $errors;

  /**
   *  __construct
   * @param string $xmlFileName
   * @param string $version
   * @param string $charset
   */
  public function __construct($version = "1.0", $charset = "utf-8") {
    libxml_use_internal_errors(true);
    $this->xml = new DOMDocument($version, $charset);
    $this->xml->preserveWhiteSpace = false;
  }

  /**
   * @static
   * @param DomDocument $dom
   * @param string $query
   * @return DOMNodeList
   */
  public static function Query(DomDocument $dom, $query) {
    $xpath = new DOMXPath($dom);
    return $xpath->query($query);
  }

  /**
   * @static
   * @param string $string
   * @return DOMDocument
   */
  public static function createDocument($string, $base='root') {
    $dom = new DOMDocument("1.0", "utf-8");
    $dom->loadXML(sprintf("<$base>%s</$base>", $string));
    return $dom;
  }

  /**
   * load
   * @param string $xml
   * @return void
   */
  public function loadXML($xml) {
    $this->getXML()->loadXML($xml);
    return $this;
  }

  /**
   * getXML
   * @param DOMDocument $xml
   * @return $xml
   */
  public function getXML() {
    return $this->xml;
  }

  /**
   * getXSL
   * @param XSLTProcessor $xsl
   * @return $xsl
   */
  public function getXSL() {
    return $this->xsl;
  }

  /**
   * XSL
   * @param string $xslFileName
   * @param integer $mode
   * @return void
   */
  public function XSL($xslFileName, $mode = LIBXML_NOCDATA) {
    $doc = new DOMDocument();
    $doc->load($xslFileName, $mode);

    $this->xsl = new XSLTProcessor();
    $this->getXSL()->importStylesheet($doc);
    $this->getXSL()->registerPHPFunctions();
    return $this;
  }

  /**
   * transformToXML
   * @return string
   */
  public function transform() {
    return $this->getXSL()->transformToXML($this->getXML());
  }

  /**
   * @param string $element
   * @return DomNode
   */
  public function createElement($element) {
    return $this->xml->createElement($element);
  }

  /**
   * @param string $name
   * @return DomAttribute
   */
  public function createAttribute($name) {
    return $this->xml->createAttribute($name);
  }

  /**
   * @param string $text
   * @return DomTextNode
   */
  public function createTextNode($text) {
    return $this->xml->createTextNode($text);
  }

  /**
   * append child to element
   * @param DomNode $parent
   * @param DomNode $obj
   */
  public function append($parent, $obj) {
    $parent->appendChild($obj);
  }

  /**
   * fetch xml validation errors from internal error stack
   */
  private function fetchErrors() {
    if (!$this->errors instanceof DomDocument)
    {
      $this->errors = new DomDocument("1.0", "utf-8");
      $errs = $this->errors->createElement("errors");
    }

    $errors = libxml_get_errors();
    foreach ($errors as $error)
    {
      $err = $this->errors->createElement("error");
      $this->errors->createTextNode($this->displayXmlError($err, $error));
      $errs->appendChild($err);
    }
    $this->errors->appendChild($errs);
    libxml_clear_errors();
  }

  /**
   *
   * @param DomNode $node
   * @param array $error
   */
  public function displayXmlError($node, $error) {
    $a = $this->errors->createAttribute("line");
    $t = $this->errors->createAttribute("code");
    $m = $this->errors->createTextNode(trim($error->message));

    $node->appendChild($a);
    $node->appendChild($t);
    $node->appendChild($m);

    $node->setAttribute("line", $error->line);

    switch ($error->level)
    {
      case LIBXML_ERR_WARNING:
        $node->setAttribute("code", $error->code);
        break;
      case LIBXML_ERR_ERROR:
        $node->setAttribute("code", $error->code);
        break;
      case LIBXML_ERR_FATAL:
        $node->setAttribute("code", $error->code);
        break;
    }
  }

}
