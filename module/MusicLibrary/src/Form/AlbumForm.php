<?php

namespace MusicLibrary\Form;

use Zend\Form\Form;

class AlbumForm extends Form {

    public function __construct($name = null) {
        // We will ignore the name provided to the constructor
        parent::__construct('album');

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
            'name' => 'artist',
            'type' => 'text',
            'options' => [
                'label' => 'Artist',
            ],
        ]);
//        $this->add
//                (
//                array
//                    (
//                    'type' => 'Zend\Form\Element\Csrf',
//                    'name' => 'prevent',
//                    'attributes' => array('type' => 'text'),
//                    'options' => array
//                        (
//                        'csrf_options' => array
//                            (
//                            'timeout' => 1200
//                        )
//                    ),
//                )
//        );
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
