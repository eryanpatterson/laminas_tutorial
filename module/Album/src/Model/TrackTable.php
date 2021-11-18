<?php
namespace Album\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class TrackTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $tracks = $this->tableGateway->select();
    }

    public function fetchAlbum($id) {
        return $this->tableGateway->select(['album' => $id]);
    }

    public function getTrack($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveTrack(Track $track) {
        $data = [
            'album' => $track->album,
            'title' => $track->title,
            'length' => $track->length,
        ];

        $id = (int) $track->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getTrack($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update track with identifier %d; does not exist.',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteTrack($id) {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}