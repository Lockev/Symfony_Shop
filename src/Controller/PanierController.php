<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Panier;

/**
 *  @Route("/{_locale}")
 */
class PanierController extends AbstractController
{
  /**
   * @Route("/", name="home")
   */
  public function index()
  {
    $em = $this->getDoctrine()->getManager();

    $panier = $em->getRepository(Panier::class)->findAll();

    return $this->render('panier/index.html.twig', [
      'panier' => $panier
    ]);
  }

  /**
   * @Route("/panier/remove/{id}", name="retirer_produit_panier")
   */
  public function remove(Panier $produit = null, TranslatorInterface $translator)
  {
    if ($produit != null) {
      $em = $this->getDoctrine()->getManager();

      if ($produit != null) {
        $em->remove($produit);
        $em->flush();
        $this->addFlash('success', $translator->trans('flash.success.productRemoved'));
      } else {
        $this->addFlash('danger', $translator->trans('flash.error.productMissing'));
      }
    }
    return $this->redirectToRoute('home');
  }

  /**
   * @Route("/panier/buy", name="acheter_panier")
   */
  public function buy(Panier $paniers = null, TranslatorInterface $translator)
  {
    $em = $this->getDoctrine()->getManager();
    $paniers = $em->getRepository(Panier::class)->findAll();

    if ($paniers != null) {
      foreach ($paniers as $panier) {
        $panier->setEtat(true);
        $em->persist($panier);
        $em->flush();
      }
      $this->addFlash("success", $translator->trans("flash.success.cartValidated"));
    } else {
      $this->addFlash("danger", $translator->trans("flash.error.cartError"));
    }
    return $this->redirectToRoute('home');
  }
}
