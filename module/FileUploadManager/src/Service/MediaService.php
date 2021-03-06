<?php

namespace MediaManager\Service;

use Zend\Db\Adapter\AdapterInterface;

class FileUploadManager {

    protected $upload_directory;

    public function __construct($upload_directory) {
        $this->upload_directory = $upload_directory;
    }

    public function prepareDirectory() {

        $directory = $this->media_directory . "/" . date("Y") . "/" . date("m") . "/";

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
            chmod($directory, 0777);
        }
    }

    public function uploadFromFiles($files) {

        $this->prepareDirectory();

        $uploaded_files = array();

        foreach ($files as $file) {
            $file_info = pathinfo($file['name']);
        }




        $file_data = $this->generateFileName($file_data);

        $new_file_name = $file_data['filename'] . '.' . $file_data['extension'];

        $media_directory = $this->getMediaDirectoryPath() . "/" . $prepared_directory_data['year'] . '/' . $prepared_directory_data['month'];

        $file_location = $media_directory . '/' . $new_file_name;

        if ($url) {
            $url_file_content = file_get_contents($files);
            file_put_contents($file_location, $url_file_content);
        } else {
            if (is_null($single_key_name)) {
                copy($files["tmp_name"], $file_location);
            } else {
                copy($files[$single_key_name]["tmp_name"], $file_location);
            }
        }
        $save_media_data["name"] = $new_file_name;
        $save_media_data["url"] = date("Y") . '/' . date("m") . '/' . $new_file_name;
        if ($url) {
            $save_media_data["type"] = $this->getMimeTypeFromExtension($file_data['extension']);
        } else {
            if (is_null($single_key_name)) {
                $save_media_data["type"] = $files['type'];
            } else {
                $save_media_data["type"] = $files[$single_key_name]['type'];
            }
        }

        return $media_data;
    }

    public function getImageAttachmentUrl($id) {
        MediasMapper::setDbAdapter($this->db);
        return MediasMapper::findOne(array(), array('id' => $id));
    }

    public function getAllMedia($id) {
        MediasMapper::setDbAdapter($this->db);
        $media_response = [];
        if (is_numeric($id)) {
            $media = MediasMapper::findOne(array(), array('id' => $id));
            $image_validator = new \Zend\Validator\File\IsImage();
            $media_directory_url = $this->getMediaDirectoryUrl();
            if ($image_validator->isValid($this->getMediaDirectoryPath() . "/" . $media['url'])) {
                $filedata = pathinfo($this->getMediaDirectoryPath() . "/" . $media['url']);
                $media_response['full'] = $media_directory_url . '/' . $media['url'];
                preg_match('/^(?<year>[0-9]{4})\/(?<month>[0-9]{1,2})/', $media['url'], $date_uploaded);
                foreach ($this->config['image_thumbnails'] as $media_thumbnail_key => $media_thumbnail_data) {
                    $media_response[$media_thumbnail_key] = $media_directory_url . '/' . $date_uploaded['year'] . '/' . $date_uploaded['month'] . '/' . $filedata['filename'] . '-' . $media_thumbnail_key . '.' . $filedata['extension'];
                }
            } else {
                $media_response = $media_directory_url . '/' . $media['url'];
            }
        }
        return $media_response;
    }

    public function getMediaDirectoryUrl() {
        return $this->config['site_url'] . '/' . $this->config['media_directory'];
    }

    public function getMediaDirectoryPath() {
        return 'public/' . $this->config['media_directory'];
    }

    public function regenerateThumnailImages($include = null) {
        $image_validator = new \Zend\Validator\File\IsImage();
        MediasMapper::setDbAdapter($this->db);

        $media = MediasMapper::findAll(array(), array(), $include);

        $media_directory = $this->getMediaDirectoryPath();
        foreach ($media as $m) {
            $file_path = $media_directory . '/' . $m['url'];
            $file_info = pathinfo($file_path);
            if ($image_validator->isValid($file_path) && file_exists($file_path)) {
                preg_match('/^(?<year>[0-9]{4})\/(?<month>[0-9]{1,2})/', $m['url'], $date_uploaded);
                foreach ($this->config['image_thumbnails'] as $media_thumbnail_key => $media_thumbnail_data) {
                    $image_manipulator = null;
                    $image_manipulator = Grafika::createEditor();
                    $image_manipulator->open($image, $file_path);
                    $img_path = $media_directory . '/' . $date_uploaded['year'] . '/' . $date_uploaded['month'] . '/' . $file_info['filename'] . '-' . $media_thumbnail_key . '.' . $file_info['extension'];
                    if (isset($media_thumbnail_data['eval_code'])) {
                        eval($media_thumbnail_data['eval_code']);
                    }
                }
            }
        }
    }

    public function createFromUrl($url = '') {
        $d = pathinfo($url);
        echo "<pre>";
        print_r($d);
        echo "</pre>";
        die;
        // download and create gd image
        $image = ImageCreateFromString(file_get_contents($url));

        // calculate resized ratio
        // Note: if $height is set to TRUE then we automatically calculate the height based on the ratio
        $height = $height === true ? (ImageSY($image) * $width / ImageSX($image)) : $height;

        // create image 
        $output = ImageCreateTrueColor($width, $height);
        ImageCopyResampled($output, $image, 0, 0, 0, 0, $width, $height, ImageSX($image), ImageSY($image));

        // save image
        ImageJPEG($output, $filename, 95);

        // return resized image
        return $output; // if you need to use it
    }

    public function getMimeTypeFromExtension($extension = '') {


        $mimet = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        if (isset($mimet[$extension])) {
            return $mimet[$extension];
        } else {
            return 'application/octet-stream';
        }
    }

    public function getMediaType($media_id = null, $single = false) {
        MediasMapper::setDbAdapter($this->db);
        $type = MediasMapper::findOne(array('type'), array('id' => $media_id));
        if ($single) {
            return $type['type'];
        } else {
            return $type;
        }
    }

}
