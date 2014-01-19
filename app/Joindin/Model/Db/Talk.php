<?php
namespace Joindin\Model\Db;

use  \Joindin\Service\Cache as CacheService;

class Talk
{
    protected $keyName = 'talks';
    protected $db;

    public function __construct($dbNum)
    {
        $this->cache = new CacheService($dbNum);
    }

    public function getUriFor($slug, $eventUri)
    {
        $data = $this->cache->loadByKeys('talks', array(
            'event_uri' => $eventUri,
            'slug' => $slug
        ));
        return $data['uri'];
    }

    public function getTalkByStub($stub)
    {
        $data = $this->db->getOneByKey($this->keyName, 'stub', $stub);
        return $data;
    }

    public function load($uri)
    {
        $data = $this->cache->load('talks', 'uri', $uri);
        return $data;
    }

    public function saveSlugToDatabase(\Joindin\Model\Talk $talk)
    {
        $data = array(
            'uri' => $talk->getApiUri(),
            'title' => $talk->getTitle(),
            'slug' => $talk->getUrlFriendlyTalkTitle(),
            'verbose_uri' => $talk->getApiUri(true),
            'event_uri' => $talk->getEventUri(),
            'stub' => $talk->getStub(),
        );

        $savedTalk = $this->load($talk->getApiUri());
        if ($savedTalk) {
            // talk is already known - update this record
            $data = array_merge($savedTalk, $data);
        }

		$keys = array(
            'event_uri' => $talk->getEventUri(),
            'slug' => $talk->getUrlFriendlyTalkTitle()
        );

        $this->cache->save('talks', $data, 'uri', $talk->getApiUri());
        $this->cache->saveByKeys('talks', $data, $keys);
    }
}