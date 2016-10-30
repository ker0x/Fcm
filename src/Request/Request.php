<?php
namespace Kerox\Fcm\Request;

use Kerox\Fcm\Message\Data;
use Kerox\Fcm\Message\Notification;
use Kerox\Fcm\Message\Options;
use Kerox\Fcm\Message\Topics;

/**
 * Class Request
 * @package Kerox\Fcm\Request
 */
class Request extends BaseRequest
{

    /**
     * @var string|array
     */
    protected $targets;

    /**
     * @var null|\Kerox\Fcm\Message\Notification
     */
    protected $notification;

    /**
     * @var null|\Kerox\Fcm\Message\Data
     */
    protected $data;

    /**
     * @var null|\Kerox\Fcm\Message\Options
     */
    protected $options;

    /**
     * @var null|\Kerox\Fcm\Message\Topics
     */
    protected $topics;

    /**
     * Request constructor.
     *
     * @param string $apiKey
     * @param string|array $targets
     * @param null|\Kerox\Fcm\Message\Notification $notification
     * @param null|\Kerox\Fcm\Message\Data $data
     * @param null|\Kerox\Fcm\Message\Options $options
     * @param null|\Kerox\Fcm\Message\Topics $topics
     */
    public function __construct(
        string $apiKey,
        $targets,
        Notification $notification = null,
        Data $data = null,
        Options $options = null,
        Topics $topics = null
    ) {
        parent::__construct($apiKey);

        $this->targets = $targets;
        $this->notification = $notification;
        $this->data = $data;
        $this->options = $options;
        $this->topics = $topics;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function buildBody(): array
    {
        $body = [
            'to' => $this->getTo(),
            'registration_ids' => $this->getRegistrationIds(),
            'notification' => $this->getNotification(),
            'data' => $this->getData(),
        ];
        $body += $this->getOptions();

        return array_filter($body);
    }

    /**
     * Return target if target is a string or topics if there is only one.
     *
     * @return string|null
     */
    public function getTo()
    {
        $to = is_array($this->targets) ? null : $this->targets;
        if ($this->topics !== null && $this->topics->hasOnlyOneTopic()) {
            $to = $this->topics->toString();
        }

        return $to;
    }

    /**
     * Return targets if targets is an array
     *
     * @return array|null
     */
    protected function getRegistrationIds()
    {
        return is_array($this->targets) ? $this->targets : null;
    }

    /**
     * Return notification as an array.
     *
     * @return array|null
     */
    protected function getNotification()
    {
        $notification = $this->notification ? $this->notification->toArray() : null;

        return $notification;
    }

    /**
     * Return data as an array.
     *
     * @return array|null
     */
    protected function getData()
    {
        $data = $this->data ? $this->data->toArray() : null;

        return $data;
    }

    /**
     * Return options as an array and merge topics as condition if there is more than one.
     *
     * @return array|null
     */
    protected function getOptions()
    {
        $options = $this->options ? $this->options->toArray() : null;
        if ($this->topics !== null && !$this->topics->hasOnlyOneTopic()) {
            $options = array_merge($options, $this->topics->toString());
        }

        return $options;
    }
}
