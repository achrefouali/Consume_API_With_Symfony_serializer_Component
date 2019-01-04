<?php
/**
 * Created by PhpStorm.
 * User: acf
 * Date: 03/01/2019
 * Time: 11:21
 */

namespace AtsBundle\Controller;


use AtsBundle\Form\FilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EntryController extends Controller
{
    private $pageLimit = 5;
    private $total_entities = 0;

    /**
     * @return \AtsBundle\Services\AtsServices|object
     */
    private function getAtsService(){
        return $this->get('application_ats_service');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request){

        if ($request->query->get('sort'))
        {
            $this->setSort($this->get('request_stack')->getCurrentRequest()->query->get('sort'), $this->get('request_stack')->getCurrentRequest()->query->get('order_by','DESC'));
        }
        $filterForm = $this->getFilterForm();
        $filters = $this->getFilters();
        $posts = $this->getAtsService()->getList($filters);

        $this->total_entities = $posts['total_result'];
        $paginatePost = $this->get('knp_paginator')
            ->paginate($posts['result'], $request->query->get('page', 1) ,$this->pageLimit);

        return $this->render('AtsBundle:Entry:list.html.twig',array(
            'post' => $paginatePost,
            'totalPost' => $this->total_entities,
            "filterForm" => $filterForm->createView()
        ));

    }

    /**
     * Store in the session service the current sort
     *
     * @param string $column The column
     * @param string $order_by The order sorting (ASC,DESC)
     */
    private function setSort($column, $order_by)
    {
        $this->get('session')->set('application_post_sort', $column);
        $this->get('session')->set('application_post_sort_orderBy', strtoupper($order_by));

    }
    /**
     * This function create form type
     * @return \Symfony\Component\Form\Form
     */
    public function getFilterForm()
    {
        $filters = $this->getFilters();
        return $this->createForm(FilterType::class, $filters, array());
    }
    /**
     * This function  get session filter
     * @return mixed
     */
    protected function getFilters()
    {
        return $this->get('session')->get('application_post_filter_type', []);
    }
    /**
     * Store in the session service the current filters
     *
     * @param array the filters
     */
    protected function setFilters($filters)
    {
        $this->get('session')->set('application_post_filter_type', $filters);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function filterAction(Request $request)
    {
        if ($request->get('reset')) {
            $this->setFilters(array());
            return $this->redirect($this->generateUrl("application_post_list"));
        }
        if ($request->getMethod() == "POST") {
            $form = $this->getFilterForm();
            $form->handleRequest($request);
            if ($form->isValid()) {
                $filters = $form->getViewData();
            }
        }
        if ($request->getMethod() == "GET") {
            $filters = $request->query->all();
        }

        if (isset($filters)) {
            $this->setFilters($filters);
        }
        return $this->redirect($this->generateUrl("application_post_list"));
    }


}