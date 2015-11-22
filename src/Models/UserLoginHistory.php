<?php

namespace UserAuth\Models;

use \Phalcon\Di;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use UserAuth\Libraries\Utils;

/**
 * Class UserLoginHistory
 * @property int id
 * @property int user_id
 * @property string date_logged
 * @property string login_status
 * @property string ip_address
 * @property string user_agent
 * @package UserAuth\Models
 * @author Tega Oghenekohwo <tega@cottacush.com>
 */
class UserLoginHistory extends BaseModel
{

    const LOGIN_STATUS_SUCCESS = 'success';

    const LOGIN_STATUS_FAILED = 'failed';

    const LOGIN_STATUS_UNKNOWN = 'unknown';

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDateLogged()
    {
        return $this->date_logged;
    }

    /**
     * @param string $date_logged
     */
    public function setDateLogged($date_logged)
    {
        $this->date_logged = $date_logged;
    }

    /**
     * @return string
     */
    public function getLoginStatus()
    {
        return $this->login_status;
    }

    /**
     * @param string $login_status
     */
    public function setLoginStatus($login_status)
    {
        $this->login_status = $login_status;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }

    /**
     * @param string $ip_address
     */
    public function setIpAddress($ip_address)
    {
        $this->ip_address = $ip_address;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * @param string $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->user_agent = $user_agent;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return "user_login_history";
    }

    /**
     * Set field before validation check
     */
    public function beforeValidationOnCreate()
    {
        $this->date_logged = Utils::getCurrentDateTime();
    }

    /**
     * Save
     * @param $data
     * @return bool
     */
    public function addLog($data)
    {
        /* @var \Phalcon\Http\Request */
        $request = Di::getDefault()->getRequest();

        if (empty($data['ip_address'])) {
            $data['ip_address'] = $request->getClientAddress();
        }

        if (empty($data['user_agent'])) {
            $data['user_agent'] = $request->getUserAgent();
        }

        return $this->save($data);
    }

    /**
     * Get an instance of this class
     * @author Tega Oghenekohwo <tega@cottacush.com>
     * @return $this
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     * @param $page
     * @param $limit
     * @return \stdClass
     */
    public function fetchLoginHistory($page, $limit)
    {
        $builder = $this->getModelsManager()->createBuilder()
            ->columns('*')
            ->where("user_id = :user_id:", ['user_id' => $this->user_id])
            ->from(UserLoginHistory::class);

        $paginator = new PaginatorQueryBuilder([
            "builder" => $builder,
            "limit"   => empty($limit) || $limit < 1 ? self::DEFAULT_PAGE_LIMIT : $limit,
            "page"    => $page < 1 ? 1 : $page
        ]);

        return $paginator->getPaginate();
    }
}