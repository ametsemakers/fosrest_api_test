<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/* Pour transformer les inputs en objet, pas neccesaire si on utilise ParamConverter */
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticleType;
use JMS\Serializer\SerializerInterface;

/* pagination */
use FOS\RestBundle\Request\ParamFetcherInterface;
use App\Representation\Articles;

/* assert validation */
use Symfony\Component\Validator\Validator\ValidatorInterface;
/* FOSRest validation */
use Symfony\Component\Validator\ConstraintViolationList;

class ArticleController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/articles/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     */
    public function showAction(Article $article)
    {
        return $article;
    }
    
    /**
     * @Rest\Get("/articles", name="app_article_list")
     * @Rest\QueryParam(
     *      name="keyword",
     *      requirements="[a-zA-Z0-9]",
     *      nullable=true,
     *      description="The Keyword to search for."
     * )
     * @Rest\QueryParam(
     *      name="order",
     *      requirements="asc|desc",
     *      default="asc",
     *      description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *      name="limit",
     *      requirements="\d+",
     *      default="15",
     *      description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *      name="offset",
     *      requirements="\d+",
     *      default="0",
     *      description="The pagination offset"
     * )
     * @Rest\View()
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('App:Article')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        /* avant class Articles */
        //return $pager->getCurrentPageResults();
        return new Articles($pager);
    }

    /**
     * @Rest\Post(
     *      path = "/articles",
     *      name = "app_article_create"
     * )
     * @Rest\View(StatusCode = 201)
     */
    public function createAction(SerializerInterface $serialize, Request $request, ConstraintViolationList $violations)
    {
        /* SerializerInterface et Request pour la méthode avec form, Article $article pour ParamConverter */
        /* ajouter dans les annotations si on utilise ParamConverter */
        /* * @ParamConverter("article", converter="fos_rest.request_body")*/

        /* ValidatorInterface $validate, pour la validation par Symfony assert */
        /* ConstraintViolationList $violations, pour la validation par FOSRest */

        /* technique input avec form */
        $data = $serialize->deserialize($request->getContent(), 'array', 'json');
        $article = new Article;
        $form = $this->get('form.factory')->create(ArticleType::class, $article);
        $form->submit($data);
        /* fin technique avec form */

        /* méthode validation Symfony assert */
        // $errors = $validate->validate($article);

        // if (count($errors))
        // {
        //     return $this->view($errors, Response::HTTP_BAD_REQUEST);
        // }

        /* méthode validation FOSRest */

        if (count($violations))
        {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }

        
        /* méthode générale */
        $em = $this->getDoctrine()->getManager();

        $em->persist($article);
        $em->flush();

        /* return avec ParamConverter */
        // return $this->view($article, Response::HTTP_CREATED, [
        //     'Location' => $this->generateUrl('app_article_show', [
        //         'id' => $article->getId(), UrlGeneratorInterface::ABSOLUTE_URL
        //         ])
        //     ])
        // ;

        /* return avec form */
        return $this->view($article, Response::HTTP_CREATED, [
            'Location' => $this->generateUrl('app_article_show', [
                'id' => $article->getId()
                ])
            ])
        ;
    }

    
}