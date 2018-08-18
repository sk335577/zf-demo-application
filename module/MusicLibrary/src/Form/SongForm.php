<?php

namespace MusicLibrary\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\FileInput;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\Validator\StringLength;
use Zend\Validator\File;

class SongForm extends Form {

    public function __construct($name = null) {
        // We will ignore the name provided to the constructor
        parent::__construct('song');

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->addElements();
        $this->addInputFilter();
    }

    private function addInputFilter() {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);


        $inputFilter->add([
            'name' => 'title',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        // Add validation rules for the "file" field.	 
        $inputFilter->add([
            'type' => FileInput::class,
            'name' => 'file',
            'required' => true,
            'validators' => [
                [
                    'name' => 'FileUploadFile'
                ],
                [
                    'name' => 'FileMimeType',
                    'options' => [
                        'mimeType' => ['audio']
                    ]
                ],
                [
                    'name' => 'FileSize',
                    'options' => [
                        'max' => '10MB',
                    ]
                ],
            ],
//            'filters' => [
//                [
//                    'name' => 'FileRenameUpload',
//                    'options' => [
//                        'target' => './public/music-library/media',
//                        'useUploadName' => true,
//                        'useUploadExtension' => true,
//                        'overwrite' => false,
//                        'randomize' => true
//                    ]
//                ]
//            ],
        ]);
    }

    public function addElements() {

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);

        $this->add([
            'name' => 'title',
            'type' => 'text',
            'options' => [
                'label' => 'Title',
            ],
        ]);

        $this->add([
            'type' => 'file',
            'name' => 'file',
            'attributes' => [
                'id' => 'file'
            ],
            'options' => [
                'label' => 'Upload file',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id' => 'submitbutton',
            ],
        ]);
    }

}
