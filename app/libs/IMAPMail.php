<?php

namespace App\Libs;

use Nette\Http\Session;
use Tracy\Debugger;

class IMAPMail extends ImapMailbox
{
    private $disabled;
    private $inbox = array();
    private $msgCount;
    private $section;

    public function __construct($host, $username, $password, $port = null, $disabled, Session $session)
    {
        $this->disabled = $disabled;

        $option = "/notls";
        $box = "INBOX";
        $host = '{' . $host . $option . '}' . $box;

        $this->section = $session->getSection("eConnect");

        if ($this->isOnline()) {
            parent::__construct($host, $username, $password, null, 'utf-8');
        }
    }

    public function isOnline()
    {
        return !$this->disabled;
    }

    public function start()
    {
        if ($this->isOnline()) {
            $this->emailConnect();
            $this->inbox();

        } else {
            $this->inbox = null;
            $this->msgCount = 0;
        }
    }

    public function emailConnect()
    {
        $this->setEConnect($this->getImapStream());
    }

    public function getEConnect()
    {
        return $this->section->val;
    }

    public function setEConnect($var)
    {
        $this->section->val = $var;
    }

    /**
     * @return mixed Informations about new Emails received
     */
    public function inbox()
    {
        $this->msgCount = $this->checkMailbox();
        $this->msgCount = count($this->searchMailbox("UNSEEN"));
        for ($i = $this->msgCount - 1; $i >= 0; $i--) {
            $uid = $this->searchMailbox("UNSEEN")[$i];
            $this->inbox[] = $this->convDateToCzech($this->getMail($uid));
            $this->markMailAsUnread($uid);
        }
    }

    public function getInbox()
    {
        return $this->inbox;
    }

    public function getMessCount()
    {
        return $this->msgCount;
    }

    public function emailVisited($id)
    {
        $this->markMailAsRead($id);
    }

    public function convDateToCzech($mail)
    {
        $date = $mail->date;
        $mail->date = $this->get_day_name($date);
        return $mail;
    }

    function get_day_name($timestamp)
    {
        $date = date("d/m/Y", strtotime($timestamp));
        if ($date == date('d/m/Y')) {
            $day_name = 'Dnes';
        } else if ($date == date('d/m/Y', time() - (24 * 60 * 60))) {
            $day_name = 'Včera';
        } else {
            $day_name = date("d. m. Y", $timestamp);
        }
        return $day_name;
    }
}