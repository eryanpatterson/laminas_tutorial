<?php

namespace Album\Form;

use Laminas\Form\Form;

class TrackForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('track');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'album',
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
            'name' => 'length',
            'type' => 'text',
            'options' => [
                'label' => 'Length',
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