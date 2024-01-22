<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;

<?php foreach ($form_types_full_names as $form_type_full_name): ?>
use <?= $form_type_full_name ?>;
<?php endforeach; ?>


<?php if (isset($repository_full_class_name)): ?>
use <?= $repository_full_class_name ?>;
<?php endif ?>
use Symfony\Component\Form\FormError;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("<?= $route_path ?>")
 */
class <?= $class_name ?> extends AbstractController<?= "\n" ?>
{
    /**
     * @Route("/", name="<?= $route_name ?>_index", methods={"GET"})
     */
<?php if (isset($repository_full_class_name)): ?>
    public function index(<?= $repository_class_name ?> $<?= $repository_var ?>): Response
    {
        return $this->render('<?= $templates_path ?>/index.html.twig', [
            '<?= $entity_twig_var_plural ?>' => $<?= $repository_var ?>->findAll(),
        ]);
    }
<?php else: ?>
    public function index(): Response
    {
        $<?= $entity_var_plural ?> = $this->getDoctrine()
            ->getRepository(<?= $entity_class_name ?>::class)
            ->findAll();

        return $this->render('<?= $templates_path ?>/index.html.twig', [
            '<?= $entity_twig_var_plural ?>' => $<?= $entity_var_plural ?>,
        ]);
    }
<?php endif ?>

    /**
     * @Route("/new", name="<?= $route_name ?>_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $<?= $entity_var_singular ?> = new <?= $entity_class_name ?>();
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>,["mode"=>"new"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $validation=true;
            <?php if ($username) {?>
            if ($entityManager->getRepository(<?= $entity_class_name ?>::class)->findOneByUsername($form->getData()->GetUserName()))
            {
                    $form->get('username')->addError(new FormError("user.username.taken"));
                    $validation=false;
            }
            <?php }?>
            <?php if ($email) {?>
            if ($entityManager->getRepository(<?= $entity_class_name ?>::class)->findOneByEmail($form->getData()->GetEmail()))
            {
                    $form->get('email')->addError(new FormError("user.email.taken"));
                    $validation=false;
            }
            <?php }?>
    

            
            if ($validation)
            {
                $user->setPassword($encoder->encodePassword($<?= $entity_var_singular ?>, $form->get('newPassword')->getData()['newPassword']));
                $entityManager->persist($<?= $entity_var_singular ?>);
                $entityManager->flush();
                return $this->redirectToRoute('<?= $route_name ?>_show',["id"=>$<?= $entity_var_singular ?>->getId()]);
            }
        }

        return $this->render('<?= $templates_path ?>/new.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
            'form' => $form->createView(),
        ]);
    }
    

    
    /**
     * @Route("/profile", methods={"GET"}, name="<?= $route_name ?>_profile")
     */
    public function userProfile(): Response
    {
        $user = $this->getUser();
        return $this->render('<?= $templates_path ?>/profile.html.twig', ['user' => $user]);
    }
    
        
    /**
     * @Route("/change-password", methods={"GET", "POST"}, name="<?= $route_name ?>_change_my_password")
     */
    public function changeMyPassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $<?= $entity_var_singular ?> = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($<?= $entity_var_singular ?>, $form->get('newPassword')->getData()));
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_logout');
        }
        return $this->render('<?= $templates_path ?>/change_password.html.twig', [
            'form' => $form->createView(),
        ]);        
    }
    
    
    
    /**
     * @Route("/{id}/change-password", methods={"GET", "POST"}, name="<?= $route_name ?>_change_password")
     */
    public function changePassword(<?= $entity_class_name ?> $<?= $entity_var_singular ?>, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(RepeatedPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($<?= $entity_var_singular ?>, $form->get('newPassword')->getData()));
            $this->getDoctrine()->getManager()->flush();
            if ($this->getUser()==$<?= $entity_var_singular ?>)
            {
                return $this->redirectToRoute('app_logout');
            }
            else
            {
                return $this->redirectToRoute('<?= $route_name ?>_show',["id"=>$<?= $entity_var_singular ?>->getId()]);
            }
        }
        return $this->render('backoffice/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
    

    /**
     * @Route("/{<?= $entity_identifier ?>}", name="<?= $route_name ?>_show", methods={"GET"})
     */
    public function show(<?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        return $this->render('<?= $templates_path ?>/show.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
        ]);
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}/edit", name="<?= $route_name ?>_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, <?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        $oldUsername=$user->GetUserName();
        $oldEmail=$user->getEmail();
    
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>, ["mode"=>"edit"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager = $this->getDoctrine()->getManager();
        
            $validation=true;
            <?php if ($username) {?>
            if ($oldUsername!=$form->getData()->GetUserName())
            {
                if ($entityManager->getRepository(<?= $entity_class_name ?>::class)->findOneByUsername($form->getData()->GetUserName()))
                {
                        $form->get('username')->addError(new FormError("user.username.taken"));
                        $validation=false;
                }
            }
            <?php }?>
            <?php if ($email) {?>
            if ($oldEmail!=$form->getData()->GetEmail())
            {
                if ($entityManager->getRepository(<?= $entity_class_name ?>::class)->findOneByEmail($form->getData()->GetEmail()))
                {
                        $form->get('email')->addError(new FormError("user.email.taken"));
                        $validation=false;
                }
            }
            <?php }?>

            if ($validation)
            {
                $entityManager->flush();
                return $this->redirectToRoute('admin_user_show',["id"=>$user->getId()]);
            }

        }

        return $this->render('<?= $templates_path ?>/edit.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}/delete", name="<?= $route_name ?>_delete", methods={"POST"})
     */
    public function delete(Request $request, <?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        if ($this->isCsrfTokenValid('delete'.$<?= $entity_var_singular ?>->get<?= ucfirst($entity_identifier) ?>(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($<?= $entity_var_singular ?>);
            $entityManager->flush();
        }

        return $this->redirectToRoute('<?= $route_name ?>_index');
    }
}
