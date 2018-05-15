<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Contact;
use App\Entity\Traits\ActivityTypeTrait;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ActivityController extends Controller
{
    use ActivityTypeTrait;

    private const PER_PAGE = 10;

    /**
     * @Route("/activities/view/{id}", name="activity_view", requirements={"id"="\d+"})
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function view(int $id): Response
    {
        /** @var $activity Activity */
        $activity = $this->getActivity($id);
        return $this->render('activity/view.html.twig', [
            'activity' => $activity,
            'form' => $this->createDeleteForm($activity)->createView()
        ]);
    }

    /**
     * @Route("/activities/delete/{id}", name="activity_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request, int $id): Response
    {
        /** @var $activity Activity */
        $activity = $this->getActivity($id);
        $form = $this->createDeleteForm($activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactId = $activity->getContact()->getId();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($activity);
            $entityManager->flush();

            $this->addFlash('notice', 'The activity was deleted');
            return $this->redirectToRoute('activity_index', ['contact' => $contactId]);
        }

        return $this->render('activity/view.html.twig', [
            'activity' => $activity,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/activities", name="activity_index")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var EntityManagerInterface $entityManager */
        $builder = $entityManager->createQueryBuilder();
        $builder->select('a')->from(Activity::class, 'a')->addOrderBy('a.createdAt', 'DESC');

        if ($contactId = $request->query->getInt('contact', false)) {
            if (!$contact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId)) {
                throw new \Exception('Contact not found with id ' . $contactId);
            }
            $builder->where($builder->expr()->eq('a.contact', ':contact'))
                ->setParameter('contact', $contact);
        }
        $searchFormData = $request->query->get('form');
        $name = $searchFormData['summary'] ?? '';
        if ($name) {
            $builder->andWhere($builder->expr()->like('a.summary', ':summary'))
                ->setParameter('summary', '%' . trim($name) . '%');
        }

        return $this->render('activity/index.html.twig', [
            'pagination' => $paginator->paginate(
                $builder->getQuery(),
                $request->query->getInt('page', 1),
                self::PER_PAGE
            ),
            'form' => $this->createSearchForm($request)->createView(),
            'contact' => $contact ?? null
        ]);
    }

    /**
     * @param Request $request
     * @return FormInterface
     */
    private function createSearchForm(Request $request): FormInterface
    {
        $form = $this->createFormBuilder(null, [
            'method' => 'GET',
            'action' => $this->generateUrl($request->get('_route'))
        ])->add('summary', SearchType::class, [
            'attr' => [
                'placeholder' => 'Search activities'
            ],
            'required' => false,
            'label' => false
        ])->getForm();

        return $form->handleRequest($request);
    }

    /**
     * @param Activity $activity
     * @return FormInterface
     */
    private function createDeleteForm(Activity $activity): FormInterface
    {
        return $this->createFormBuilder(null, [
            'action' => $this->generateUrl('activity_delete', ['id' => $activity->getId()]),
            'method' => 'DELETE'
        ])->getForm();
    }

    /**
     * @param int $id
     * @return Activity
     * @throws \Exception
     */
    private function getActivity(int $id)
    {
        /** @var $activity Activity */
        if (!$activity = $this->getDoctrine()->getRepository(Activity::class)->find($id)) {
            throw new \Exception('Activity not found with id ' . $id);
        }
        return $activity;
    }

    /**
     * @Route("/activities/add", name="activity_add")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request): Response
    {
        $activity = new Activity();
        $choices = array_flip($activity->getActivityTypes());
        $form = $this->createFormBuilder($activity)
            ->add('summary')
            ->add('type', ChoiceType::class, [
                'choices' => $choices
            ])
            ->add('contact', EntityType::class, [
                'class' => Contact::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false
            ])
            ->add('transcript')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activity);
            $entityManager->flush();

            $this->addFlash('notice', 'The activity was added');
            return $this->redirectToRoute('activity_view', ['id' => $activity->getId()]);
        }
        return $this->render('activity/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}