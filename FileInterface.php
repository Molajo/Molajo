<?php
namespace
interface FileSystemInterface
{
    public function exists($name);

    public function getMetadata($name);  // folder, file hash, last_accessed, last_updated, size, mimetype, absolute path, relative path, filename, extension
    public function getPermissions($name); // read, update, execute, group, and owner
    public function setPermissions($path, $name, $permission); // read, update, execute, group, and owner

}

interface FileInterface extends FileSystemInterface
{
    public function isFile($path);

    public function read($path, $name);
    public function save($path, $name, $data, $create_folders);    //create or update
    public function copy($path, $name, $destination, $replace,  $create_folders);
    public function move($path, $name, $destination, $replace,  $create_folders);
    public function delete($path, $name);

    public function getFilename($name);
    public function getPath($name, $path_type); //absolute, relative
    public function getExtension($name);
}

interface FolderInterface extends FIeldSystemInterface
{

    public function getList($path, $recursive, $include_files, $mask);

    public function save($path, $name, $create_folders);    //create or update
    public function copy($path, $destination, $replace,  $create_folders);
    public function move($path, $destination, $replace,  $create_folders);
    public function rename($path, $name, $new_name);
    public function delete($path, $delete_files, $recursive);

    public function getFoldername($path);
    public function getPath($path, $path_type); //absolute, relative
}


abstract class File implements FileInterface
{
    private $vars = array();

    public function setVariable($name, $var)
    {
        $this->vars[$name] = $var;
    }

    public function getHtml($template)
    {
        foreach($this->vars as $name => $value) {
            $template = str_replace('{' . $name . '}', $value, $template);
        }

        return $template;
    }
}



abstract class Folder implements FolderInterface
{
    private $vars = array();

    public function setVariable($name, $var)
    {
        $this->vars[$name] = $var;
    }

    public function getHtml($template)
    {
        foreach($this->vars as $name => $value) {
            $template = str_replace('{' . $name . '}', $value, $template);
        }

        return $template;
    }
}
