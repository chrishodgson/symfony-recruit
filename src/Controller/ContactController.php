<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Company;
use App\Entity\Contact;
use App\Repository\ActivityRepository;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * @Route("/contacts/view/{id}", name="contact_view", requirements={"id"="\d+"})
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function view(int $id): Response
    {
        /** @var $contact Contact */
        $contact = $this->getContact($id);
        return $this->render('contact/view.html.twig', [
            'contact' => $contact,
            'form' => $this->createDeleteForm($contact)->createView()
        ]);
    }

    /**
     * @Route("/contacts/delete/{id}", name="contact_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request, int $id): Response
    {
        /** @var $contact Contact */
        $contact = $this->getContact($id);
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getDoctrine()->getRepository(Contact::class)->getActivityCount($contact)) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($contact);
                $entityManager->flush();

                $this->addFlash('notice', 'The contact was deleted');
                return $this->redirectToRoute('contact_index');
            }
            $this->addFlash('error', 'The contact cannot be deleted as there are activities associated');
        }

        return $this->render('contact/view.html.twig', [
            'contact' => $contact,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/contacts", name="contact_index")
     * @param Request $request ,
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $this->getPagination($request, $paginator, false);
        $this->transformPagination($pagination, false);
        return $this->render('contact/index.html.twig', [
            'pagination' => $pagination,
            'form' => $this->createSearchForm($request)->createView()
        ]);
    }

    /**
     * @Route("/contacts/latest_activity", name="contact_latest_activity_index")
     * @param Request $request ,
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws \Exception
     */
    public function latestActivityIndex(Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $this->getPagination($request, $paginator, true);
        $this->transformPagination($pagination, true);
        return $this->render('contact/latest_activity.html.twig', [
            'pagination' => $pagination,
            'form' => $this->createSearchForm($request)->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return PaginationInterface
     * @param bool $latestActivity
     * @throws \Exception
     */
    private function getPagination(
        Request $request,
        PaginatorInterface $paginator,
        bool $latestActivity
    ): PaginationInterface {
        $entityManager = $this->getDoctrine()->getManager();
        /** @var EntityManagerInterface $entityManager */
        $builder = $entityManager->createQueryBuilder();
        $builder->select('c')
            ->from(Contact::class, 'c')
            ->addOrderBy('c.latestActivityAt', 'DESC');

        if ($latestActivity) {
            $builder->andWhere($builder->expr()->isNotNull('c.latestActivityAt'));
        }

        if ($companyId = $request->query->getInt('company', false)) {
            if (!$company = $this->getDoctrine()->getRepository(Company::class)->find($companyId)) {
                throw new \Exception('Company not found with id ' . $companyId);
            }
            $builder->andWhere($builder->expr()->eq('c.company', ':company'))
                ->setParameter('company', $company);
        }

        $searchFormData = $request->query->get('form');
        $notify = $searchFormData['notify'] ?? 'all';
        if (in_array($notify, ['no', 'yes'])) {
            $builder->andWhere($builder->expr()->eq('c.notifyWhenAvailable', $notify == 'yes' ? 1 : 0));
        }

        $name = $searchFormData['name'] ?? '';
        if ($name) {
            $builder->andWhere($builder->expr()->like('c.name', ':name'))
                ->setParameter('name', '%' . trim($name) . '%');
        }
        return $paginator->paginate(
            $builder->getQuery(),
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );
    }

    /**
     * @param PaginationInterface $pagination
     * @param bool $latestActivity
     */
    private function transformPagination(PaginationInterface $pagination, bool $latestActivity): void
    {
        $results = [];
        $contactRepository = $this->getDoctrine()->getRepository(Contact::class);

        /** @var AbstractPagination $pagination */
        foreach ($pagination->getItems() as $item) {
            /** @var Contact $item */
            $data = $item->toArray();
            /** @var ContactRepository $contactRepository */
            $data['activityCount'] = $contactRepository->getActivityCount($item);
            if ($latestActivity) {
                $data['latestActivity'] = $this->getLatestActivity($item);
            }
            $results[] = (object)$data;
        }
        $pagination->setItems($results);
    }

    /**
     * @param Contact $contact
     * @return Activity|null
     */
    private function getLatestActivity(Contact $contact)
    {
        /** @var ActivityRepository $activityRepository */
        $activityRepository = $this->getDoctrine()->getRepository(Activity::class);
        $results = $activityRepository->findBy(['contact' => $contact], ['createdAt' => 'DESC'], 1);
        return $results[0] ?? null;
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
        ])->add('name', SearchType::class, [
            'attr' => [
                'placeholder' => 'Search contact name'
            ],
            'required' => false,
        ])->add('notify', ChoiceType::class, [
            'choices' => [
                'All contacts' => 'all',
                'Contacts on notify list' => 'yes',
                'Contacts not on notify list' => 'no'
            ],
            'required' => false,
            'label' => false,
            'empty_data' => 'all',
        ])->getForm();

        return $form->handleRequest($request);
    }

    /**
     * @param Contact $contact
     * @return FormInterface
     */
    private function createDeleteForm(Contact $contact): FormInterface
    {
        return $this->createFormBuilder(null, [
            'action' => $this->generateUrl('contact_delete', ['id' => $contact->getId()]),
            'method' => 'DELETE'
        ])->getForm();
    }

    /**
     * @param int $id
     * @return Contact
     * @throws \Exception
     */
    private function getContact(int $id)
    {
        /** @var $contact Contact */
        if (!$contact = $this->getDoctrine()->getRepository(Contact::class)->find($id)) {
            throw new \Exception('Contact not found with id ' . $id);
        }
        return $contact;
    }

    /**
     * @Route("/contacts/add", name="contact_add")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createFormBuilder($contact)
            ->add('company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'required' => false
            ])
            ->add('name')
            ->add('email')
            ->add('landline')
            ->add('mobile')
            ->add('linkedin')
            ->add('role')
            ->add('details')
            ->add('notify_when_available', CheckboxType::class, [
                'required' => false
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('notice', 'The contact was added');
            return $this->redirectToRoute('contact_view', ['id' => $contact->getId()]);
        }
        return $this->render('contact/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
