<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;
<?php if (isset($repository_full_class_name)): ?>
use <?= $repository_full_class_name ?>;
<?php endif ?>
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

<?php if ($with_voter) {?>use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;<?php }?>   


 #[Route(path: '<?= $route_path ?>')]
class <?= $class_name ?> extends AbstractController<?= "\n" ?>
{
    #[Route(path: '/', name: '<?= $route_name ?>_index')]
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

 
    #[Route(path: '/new', name: '<?= $route_name ?>_new')]
    public function new(Request $request, <?= $repository_class_name ?> $<?= $repository_var ?>): Response
    {
        $<?= $entity_var_singular ?> = new <?= $entity_class_name ?>();
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $<?= $repository_var ?>->save($<?= $entity_var_singular ?>, true);
            return $this->redirectToRoute('<?= $route_name ?>_index');
        }

        return $this->render('<?= $templates_path ?>/new.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{<?= $entity_identifier ?>}', name: "<?= $route_name ?>_show")]
    public function show(<?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        return $this->render('<?= $templates_path ?>/show.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
        ]);
    }

    
    
   

    #[Route(path: '/{<?= $entity_identifier ?>}/edit', name: '<?= $route_name ?>_edit')]
<?php if ($with_voter) {?>     #[IsGranted("edit", subject:"<?= $entity_var_singular ?>", message:"crud.edit.nogranted")]
<?php }?>
    public function edit(Request $request, <?= $repository_class_name ?> $<?= $repository_var ?>,  <?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
    
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $<?= $repository_var ?>->save($<?= $entity_var_singular ?>, true);
            return $this->redirectToRoute('<?= $route_name ?>_index');
        }

        return $this->render('<?= $templates_path ?>/edit.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
            'form' => $form->createView(),
        ]);
    }

    
     
    #[Route(path: '/{<?= $entity_identifier ?>}/delete', name: '<?= $route_name ?>_delete', methods: ['POST'])]
<?php if ($with_voter) {?>     #[IsGranted("edit", subject:"<?= $entity_var_singular ?>", message:"crud.edit.nogranted")]
<?php }?>
    public function delete(Request $request, <?= $repository_class_name ?> $<?= $repository_var ?>, <?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        if ($this->isCsrfTokenValid('delete'.$<?= $entity_var_singular ?>->get<?= ucfirst($entity_identifier) ?>(), $request->request->get('_token'))) {
            $<?= $repository_var ?>->remove($<?= $entity_var_singular ?>, true);
        }

        return $this->redirectToRoute('<?= $route_name ?>_index');
    }
}


