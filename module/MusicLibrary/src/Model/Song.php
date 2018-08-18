<?php

namespace MusicLibrary\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;
use Zend\Validator\File;

class Song {

    public $id;
    public $title;
    public $file;
    public $album;
    private $inputFilter;

    public function exchangeArray(array $data) {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->title = !empty($data['title']) ? $data['title'] : null;
        $this->album = !empty($data['album']) ? $data['album'] : null;
    }

    public function getArrayCopy() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'album' => $this->album,
            'file' => $this->file,
        ];
    }

}
