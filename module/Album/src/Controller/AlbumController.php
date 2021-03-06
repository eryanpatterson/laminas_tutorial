<?php 
namespace Album\Controller;

use Album\Model\AlbumTable;
use Album\Model\TrackTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Album\Form\AlbumForm;
use Album\Form\TrackForm;
use Album\Model\Album;
use Album\Model\Track;

class AlbumController extends AbstractActionController
{
    private $table;
    private $trackTable;

    public function __construct(AlbumTable $table, TrackTable $trackTable) {
        $this->table = $table;
        $this->trackTable = $trackTable;
    }
    
    public function indexAction()
    {
        if (!$this->lmcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('application');
        }
        return new ViewModel([
            'albums' => $this->table->fetchAll(),
        ]);
    }    

    public function addAction()
    {
        if (!$this->lmcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('application');
        }
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $album = new Album();
        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $album->exchangeArray($form->getData());
        $this->table->saveAlbum($album);
        return $this->redirect()->toRoute('album');
    }

    public function editAction()
    {
        if (!$this->lmcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('application');
        }
        
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('album', ['action' => 'add']);
        }

        try {
            $album = $this->table->getAlbum($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('album', ['action' => 'index']);
        }

        $form = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $album->getArrayCopy();

        try {
            $this->table->saveAlbum($album);
        } catch (\Exception $e) {
        }

        // Redirect to album list
        return $this->redirect()->toRoute('album', ['action' => 'index']);
    }

    public function deleteAction()
    {
        if (!$this->lmcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('application');
        }
        
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteAlbum($id);
            }
            return $this->redirect()->toRoute('album');
        }
        return [
            'id' => $id,
            'album' => $this->table->getAlbum($id),
        ];
    }

    public function tracksAction()
    {
        if (!$this->lmcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('application');
        }

        $id = $this->params()->fromRoute('id', null);
    
        if (!$id) {
            return $this->redirect()->toRoute('album');
        } else {
            $album = $this->table->getAlbum($id);
            return new ViewModel([
                'tracks' => $this->trackTable->fetchAlbum($id),
                'album' => $album
            ]);
        }

    }

    public function newtrackAction()
    {
        if (!$this->lmcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('application');
        }

        $album = (int) $this->params()->fromRoute('id', null);

        $form = new TrackForm($album);
        $form->get('submit')->setValue('Add');
        $form->get('album')->setValue($album);

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form, 'album' => $album];
        }

        $track = new Track();
        $form->setInputFilter($track->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form, 'album' => $album];
        }

        $track->exchangeArray($form->getData());
        $this->trackTable->saveTrack($track);
        return $this->redirect()->toRoute('album');
    }

    public function edittrackAction()
    {
        if (!$this->lmcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('application');
        }
        
        $id = (int) $this->params()->fromRoute('id', null);

        if ($id === 0) {
            return $this->redirect()->toRoute('album', ['action' => 'index']);
        }

        try {
            $track = $this->trackTable->getTrack($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('album', ['action' => 'index']);
        }

        $form = new TrackForm();
        $form->bind($track);
        $form->get('submit')->setAttribute('value', 'Edit');
        $form->get('album')->setValue($track->album);

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($track->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $track->getArrayCopy();

        try {
            $this->trackTable->saveTrack($track);
        } catch (\Exception $e) {
        }

        return $this->redirect()->toRoute('album', ['action' => 'tracks', 'id' => $track->album]);
    }

    public function deletetrackAction()
    {
        if (!$this->lmcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('application');
        }
        
        $id = (int) $this->params()->fromRoute('id', null);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $track = $this->trackTable->getTrack($id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->trackTable->deleteTrack($id);
            }

            return $this->redirect()->toRoute('album', ['action' => 'tracks', 'id' => $track->album]);
        }

        return [
            'id' => $id,
            'track' => $track,
        ];
    }

}
?>