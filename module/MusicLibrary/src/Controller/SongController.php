<?php

namespace MusicLibrary\Controller;

use MusicLibrary\Model\SongTable;
use MusicLibrary\Model\Song;
use MusicLibrary\Form\SongForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SongController extends AbstractActionController {

    private $table;

    public function __construct(SongTable $table) {
        $this->table = $table;
    }

    public function indexAction() {
        // Grab the paginator from the AlbumTable:
        $paginator = $this->table->fetchAll(true);

        // Set the current page to what has been passed in query string,
        // or to 1 if none is set, or the page is invalid:
        $page = (int) $this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);

        // Set the number of items per page to 10:
        $paginator->setItemCountPerPage(10);

        return new ViewModel(['paginator' => $paginator]);
    }

    public function addAction() {
        $request = $this->getRequest();
        $form = new SongForm();

        if ($request->isGet()) {
            $form->get('submit')->setValue('Add');
            return ['form' => $form];
        } else {
            if ($request->isPost()) {
                $data = array_merge_recursive($request->getPost()->toArray(), $request->getFiles()->toArray());
                $form->setData($data);
                if (!$form->isValid()) {
                    return ['form' => $form];
                }
                $file_data = pathinfo($_FILES['file']['name']);
                echo "<pre>";
                print_r($file_data);
                print_r($_FILES);
                echo "</pre>";
                $song = new Song();
//                $form->setInputFilter($song->getInputFilter());
//                $form->setData($request->getPost());
//                if (!$form->isValid()) {
//                    return ['form' => $form];
//                }

                $song->exchangeArray($form->getData());
                $this->table->saveSong($song);
                return $this->redirect()->toRoute('music-library/songs');
            }
        }
    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('album', ['action' => 'add']);
        }

        // Retrieve the album with the specified id. Doing so raises
        // an exception if the album is not found, which should result
        // in redirecting to the landing page.
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

        if (!$request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return $viewData;
        }

        $this->table->saveAlbum($album);

        // Redirect to album list
        return $this->redirect()->toRoute('album', ['action' => 'index']);
    }

    public function deleteAction() {
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

            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }

        return [
            'id' => $id,
            'album' => $this->table->getAlbum($id),
        ];
    }

}
