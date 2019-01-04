<?php
/**
 * this service is for global crud requirements' functionalities
 */

namespace AtsBundle\Services;

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CrudService
{
    private $container;
    /**
     * Construct
     * @param type $container
     */
    public function __construct($container) {
        $this->container = $container;
    }

    public function getContainer(){
        return $this->container ;
}

    /**
     * Check crsf token provided for action
     *
     * @throw InvalidCsrfTokenException if token is invalid
     */
    public function isGeneratedCsrfTokenValid($intention)
    {
        // to prevent problems whe calling sub-requests
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $token = $request->request->get('_csrf_token');
        
        if (!$this->container->get('security.csrf.token_manager')->isTokenValid(new CsrfToken($intention[0], $token))) {
            throw new InvalidCsrfTokenException('InvalidCsrfTokenException');
        }
    }

    /**
     * Function add new zone
     * @param $user
     * @throws ModelManagerException
     */

    public function remove($object){

            $em = $this->container->get('doctrine')->getManager();
            $em->remove($object[0]);
            $em->flush();


    }


    /**
     * Return the stored sort
     *
     * @return string The column to sort
     */
    public function getSortColumn($sessionKey, $default = 'id')
    {

        return $this->getSession()->get($sessionKey, $default);
    }
    /**
     * Return the stored sort orderBy
     *
     * @return string the orderBy mode ASC|DESC
     */
    public function getSortOrder($sessionKey, $default = 'DESC')
    {
        return strtoupper($this->getSession()->get($sessionKey, $default));
    }

    /**
     * Get Session Service
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session;
     */
    public function getSession(){
       return $this->container->get('session');
    }

    /**
     * Get Translator Service
     *
     * @return \Symfony\Component\HttpFoundation\Session\Session;
     */
    public function getTranslator(){
       return $this->container->get('translator');
    }

    /**
     * Adds a flash message to the current session for type.
     *
     * @param string $type    The type
     * @param string $message The message
     *
     * @throws \LogicException
     */
    public function addFlash($type, $message)
    {
        if (!$this->container->has('session')) {
            throw new \LogicException('You can not use the addFlash method if sessions are disabled.');
        }

        $this->getSession()->getFlashBag()->add($type, $message);
    }

    public function redirect($route, $parameters= array(), $status = 302){
        return new RedirectResponse($this->container->get('router')->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH), $status);
        
    }
}