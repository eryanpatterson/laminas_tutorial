<?php
namespace Album\Controller;

use Album\Model\TrackTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Album\Form\TrackForm;
use Album\Model\Track;

class TrackController extends AbstractActionController
{
    private $table;

    public function __construct(TrackTable $table) {
        $this->table = $table;
    }

    public function indexAction() {
        $album = $this->params()->fromRoute('album', null);

        if (!$album) {
            return new ViewModel([
                'tracks' => $this->table->fetchAll(),
            ]);
        } else {
            return new ViewModel([
                'tracks' => $this->table->fetchAlbum($album),
                'album' => $album
            ]);
        }
    }

    public function addAction() {
        $album = $this->params()->fromRoute('album', null);

        $form = new TrackForm();
        $form->get('submit')->setValue('Add');
        $form->get('album')->setValue($album);

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $track = new Track();
        $form->setInputFilter($track->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $track->exchangeArray($form->getData());
        $this->table->saveTrack($track);
        return $this->redirect()->toRoute('album');
    }
}