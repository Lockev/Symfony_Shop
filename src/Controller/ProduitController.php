<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Entity\Panier;
use App\Form\PanierType;
use Symfony\Component\HttpFoundation\Request;

/**
 *  @Route("/{_locale}")
 */
class ProduitController extends AbstractController
{
  /**
   * @Route("/produits", name="produits")
   */
  public function index(Request $request, TranslatorInterface $translator)
  {

    $em = $this->getDoctrine()->getManager();

    $produit = new Produit();
    $form = $this->createForm(ProduitType::class, $produit);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

      $file = $form->get('photoUpload')->getData();

      if ($file) {
        $fileName = uniqid() . '.' . $file->guessExtension();

        try {
          $file->move(
            $this->getParameter('upload_dir'),
            $fileName
          );
        } catch (FileException $e) {
          $this->addFlash('danger', $translator->trans('flash.error.uploadPicture'));
          return $this->redirectToRoute('produits');
        }

        $produit->setPhoto($fileName);
      }
      $em->persist($produit);
      $em->flush();
      $this->addFlash("success", $translator->trans('flash.success.productAdded'));
    }

    $produits = $em->getRepository(Produit::class)->findAll();

    return $this->render('produit/index.html.twig', [
      'produits' => $produits,
      'form_ajout' => $form->createView()
    ]);
  }

  /**
   * @Route("/produit/{id}", name="produit")
   */
  public function produit(Produit $produit = null, Request $request, TranslatorInterface $translator)
  {
    if ($produit != null) {
      $panier = new Panier($produit);
      $form = $this->createForm(PanierType::class, $panier);

      $em = $this->getDoctrine()->getManager();

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        if ($produit->getStock() > $panier->getQte()) {
          $em->persist($panier);
          $em->flush();
          $this->addFlash('success', $translator->trans('flash.success.productAddedToCart'));
        } else {
          $this->addFlash('danger', $translator->trans('flash.error.productStock'));
        }
      }

      return $this->render('produit/produit.html.twig', [
        'produit' => $produit,
        'form_panier' => $form->createView()
      ]);
    } else {
      $this->addFlash("danger", $translator->trans('flash.error.productMissing'));
      return $this->redirectToRoute('produits');
    }
  }

  /**
   * @Route("/produit/delete/{id}", name="supprimer_produit")
   */
  public function delete(Produit $produit = null, TranslatorInterface $translator)
  {
    if ($produit != null) {
      $em = $this->getDoctrine()->getManager();
      if ($produit->getPhoto() != null) {
        unlink($this->getParameter('upload_dir') . $produit->getPhoto());
      }
      $em->remove($produit);
      $em->flush();
      $this->addFlash('success', $translator->trans('flash.success.productDeleted'));
    } else {
      $this->addFlash('danger', $translator->trans('flash.error.productMissing'));
    }
    return $this->redirectToRoute('produits');
  }
};
