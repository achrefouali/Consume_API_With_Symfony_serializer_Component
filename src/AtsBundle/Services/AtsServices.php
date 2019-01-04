<?php
/**
 * Created by PhpStorm.
 * User: acf
 * Date: 03/01/2019
 * Time: 11:24
 */

namespace AtsBundle\Services;


use AtsBundle\Entity\Author;
use AtsBundle\Entity\Entry;
use AtsBundle\Entity\Feed;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AtsServices
{


    private $em;

    /**
     * Construct
     * @param EntityManager $em
     * @param CrudService $application_crud
     */
    public function __construct(EntityManager $em, CrudService $application_crud)
    {

        $this->em = $em;
        $this->application_crud = $application_crud;
    }


    /**
     * Function get List subjects  with options
     * @param array | $filter filter with options
     * @return mixed
     */
    public function getList($filter = array(), $paginator = false)
    {

        return $this->em->getRepository("AtsBundle:Entry")
            ->findRecordsByFilter(
                $filter,
                $this->application_crud->getSortColumn('application_subject_sort', 'id'),
                $this->application_crud->getSortOrder('application_subject_sort_orderBy'),
                $paginator
            );

    }
    /**
     * @param $url
     * @param bool $data
     * @param string $method
     * @param array $header
     * @param bool $body
     * @return array
     */
    function client($url, $data = false, $method = 'GET', $header = [], $body = false)
    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // To delete in prod for dev purpose only
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // To delete in prod for dev purpose only

        switch ($method) {
            case "POST" :
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, ($body) ? json_encode($data) : http_build_query($data));
                }
                break;
            case "PATCH" :
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, ($body) ? json_encode($data) : http_build_query($data));
                }
                break;
            case "PUT" :
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default :
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                break;
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ['success' => false, "response" => "cURL Error #:".$err];
        }

        return ['success' => true, "response" => $response];
    }

    /**
     * @param $url
     * @return mixed
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persistData($url){
        $serializer = new Serializer([new ObjectNormalizer()], [new XmlEncoder(), new JsonEncoder()]);
        $result = $this->client($url);
        $i=0;
        if ($result['success']) {
            $resultDecode = $serializer->deserialize($result['response'],Feed::class,'xml') ;
            $feed = $this->createFeed($resultDecode);
            foreach($resultDecode->getEntry() as $item){
                $author =  $this->createAuthor($item);
                $this->createEntry($item,$author,$feed);
                $i++;
            }
            $this->em->flush();
        }
        return ['result'=>$result['success'],'count'=>$i];
    }

    /**
     * @param $array
     * @return Feed
     */
    public function createFeed($array){
        $feed= new Feed();
        $feed->setCategory($array->getCategory()["@term"]);
        $feed->setTitle($array->getTitle());
        $this->em->persist($feed);
        return $feed ;
    }

    /**
     * @param $array
     * @return Author
     */
    public function createAuthor ($array){
        $author = new Author();
        $author->setName($array['author']['name']);
        $author->setUri($array['author']['uri']);
        $this->em->persist($author);
        return $author ;
    }

    /**
     * @param $array
     * @param $author
     * @param $feed
     * @return Entry
     */
    public function createEntry($array,$author,$feed){
        $entry = new Entry();
        $entry->setTitle($array['title']);
        $entry->setCategory($array['category']['@term']);
        $entry->setUpdated($array['updated']);
        $entry->setAuthor($author);
        $entry->setFeed($feed);
        $this->em->persist($entry);
        return $entry ;
    }

}