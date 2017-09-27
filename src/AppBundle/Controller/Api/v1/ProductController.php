<?php

namespace AppBundle\Controller\Api\v1;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @Route("/api/v1/product")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {

        // Get the Query Parameters from the URL
        // We will trust that the input is safe (sanitized)
        $sku = $request->query->get('sku');
        $name = $request->query->get('name');
        $price = number_format($request->query->get('price'),2);
        $orderby = ( "" !== $request->query->get('orderby') ? $request->query->get('orderby') : 0);
        $order = ( "" !== $request->query->get('order') ? $request->query->get('order') : 0);

        // Create a new empty object
        $product = new Product();

        // Use methods from the Product entity to set the values
        $product->setSku($sku);
        $product->setName($name);
        $product->setPrice($price);

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Add our product to Doctrine so that it can be saved
        $em->persist($product);

        // Save our product
        $em->flush();

        $data = [
            'id' => $product->getId(),
            'sku' => $product->getSku(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
        ];

        // return new Response(json_encode($data), Response::HTTP_CREATED, array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'application/json'));
        if($orderby) {
            if("desc" == $order) {
                getCollectionOrderedDescAction($orderby);
            } else {
                getCollectionOrderedAscAction($orderby);
            }
        } else {
            return $this->getCollectionAction();
        }

    }

    /**
     * @Route("/api/v1/product")
     * @Method("GET")
     */
    public function getCollectionAction()
    {
        $products = $this->getDoctrine()->getRepository('AppBundle:Product')->findAll();

        if(empty($products)) {
            return new Response("No Products Found", Response::HTTP_NOT_FOUND, array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'text/plain'));
        }

        $collection = array();

        foreach($products as $product) {
            $data = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => number_format($product->getPrice(),2),
            ];

            array_push($collection, $data);
        }

        $return = [
            'total_count' => sizeof($collection),
            'items' => $collection,
        ];

        return new Response(json_encode($return), Response::HTTP_OK, array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'application/json'));
    }

    /**
     * @Route("/api/v1/product/{id}")
     * @Method("GET")
     * @param $id
     */
    public function getAction($id)
    {
        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

        if(empty($product)) {
            return new Response("Product Not Found", Response::HTTP_NOT_FOUND, array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'text/plain'));
        }

        $data = [
            'sku' => $product->getSku(),
            'name' => $product->getName(),
            'price' => number_format($product->getPrice(),2),
        ];

        return new Response(json_encode($data), Response::HTTP_OK, array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'application/json'));
    }

    /**
     * @Route("/api/v1/product/update")
     * @Method("POST")
     */
    public function updateAction(Request $request)
    {
        // Get the Query Parameters from the URL
        // We will trust that the input is safe (sanitized)
        $id = $request->query->get('id');
        $sku = $request->query->get('sku');
        $name = $request->query->get('name');
        $price = number_format($request->query->get('price'),2);
        $orderby = ( "" !== $request->query->get('orderby') ? $request->query->get('orderby') : 0);
        $order = ( "" !== $request->query->get('order') ? $request->query->get('order') : 0);
        
        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

        if(empty($product)) {
            return new Response("Product Not Found", Response::HTTP_NOT_FOUND, array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'text/plain'));
        }

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        $void = true;

        if(!empty($sku)) {
            $void = false;
            $product->setSku($sku);
        }
        if(!empty($name)) {
            $void = false;
            $product->setName($name);
        }
        if(!empty($price)) {
            $void = false;
            $product->setPrice($price);
        }

        if(!$void) {
            $em->flush();
            // return new Response("Product Updated Successfully", Response::HTTP_OK, array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'text/plain'));
            if($orderby) {
                if("desc" == $order) {
                    getCollectionOrderedDescAction($orderby);
                } else {
                    getCollectionOrderedAscAction($orderby);
                }
            } else {
                return $this->getCollectionAction();
            }
        }

        return new Response("Parameters Can Not Be Empty", Response::HTTP_NOT_ACCEPTABLE, array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'text/plain'));
    }

    /**
     * @Route("/api/v1/product/delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request)
    {
        // Get the Query Parameters from the URL
        // We will trust that the input is safe (sanitized)
        $id = $request->query->get('id');
        $orderby = ( "" !== $request->query->get('orderby') ? $request->query->get('orderby') : 0);
        $order = ( "" !== $request->query->get('order') ? $request->query->get('order') : 0);

        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->findOneBy(['id' => $id]);

        if(empty($product)) {
            return new Response("Product Not Found", Response::HTTP_NOT_FOUND, array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'text/plain'));
        }

        // Get the Doctrine service and manager
        $em = $this->getDoctrine()->getManager();

        // Remove our product from Doctrine so that it can be deleted
        $em->remove($product);

        // Delete our product
        $em->flush();

        // return new Response("Product Deleted", Response::HTTP_OK, array('Access-Control-Allow-Origin' => '*', 'Content-Type' => 'text/plain'));
        if($orderby) {
            if("desc" == $order) {
                getCollectionOrderedDescAction($orderby);
            } else {
                getCollectionOrderedAscAction($orderby);
            }
        } else {
            return $this->getCollectionAction();
        }
    }

}
