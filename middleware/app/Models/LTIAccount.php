<?php
// This is a model for an LTI user account as defined by the Canvas API
namespace App\Models;

use Http\Controllers\LtiController\getLtiAccount;
use Illuminate\Database\Eloquent\Model;

class LTIUser extends Model
{
    // This is an example. I know db table names are probably wrong

    protected $table = 'lti_user_accounts';
    protected $fillable = ['id', 'name', 'uuid', "parentAccountId", "rootAccountId", "workflowState"];

    private $id;
    private $name;
    private $uuid;
    private $parentAccountId;
    private $rootAccountId;
    private $workflowState;

    /**
     * LTI User Account Constructor
     * @param $id   integer
     * @param $name string
     * @param $uuid string
     * @param $parentAccountId integer
     * @param $rootAccountId integer
     * @param $workflowState string
     *
     * @return void
     */
    public function __construct($id, $name, $uuid, $parentAccountId, $rootAccountId, $workflowState)
    {
        $this->id = $id;
        $this->name = $name;
        $this->uuid = $uuid;
        $this->parentAccountId = $parentAccountId;
        $this->rootAccountId = $rootAccountId;
        $this->workflowState = $workflowState;
    }

    public function checkAndInsert()
    {
        $user = self::where('id', $this->getId())
            ->where('name', $this->getAccountName())
            ->where('uuid', $this->getUuid())
            ->where('parentAccountId', $this->getParentAccountId())
            ->where('rootAccountId', $this->getRootAccountId())
            ->where('workflowState', $this->getWorkflowState())
            ->first();

        if (!$user) {
            self::create([
                'id' => $this->getId(),
                'name' => $this->getName(),
                'uuid' => $this->getUuid(),
                'parentAccountId' => $this->getParentAccountId(),
                'rootAccountId' => $this->getRootAccountId(),
                'workflowState' => $this->getWorkflowState(),
            ]);
            return true; // Data inserted
        }

        return false; // Data already exists
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    public function setParentAccountId($id)
    {
        $this->parentAccountId = $id;
    }

    public function setRootAccountId($id)
    {
        $this->rootAccountId = $id;
    }

    public function setWorkflowState($state)
    {
        $this->workflowState = $state;
    }
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getParentAccountId()
    {
        return $this->parentAccountId;
    }

    public function getRootAccountId()
    {
        return $this->rootAccountId;
    }

    public function getWorkflowState()
    {
        return $this->workflowState;
    }
}
