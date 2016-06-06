<?php

namespace Alpixel\Bundle\ShopBundle\Form;

use Alpixel\Bundle\ShopBundle\Helper\Cart\CartManager;
use Doctrine\ORM\EntityManager;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Formulaire permettant de recalculer le panier.
 *
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class CartQuantityForm extends AbstractType implements TranslationContainerInterface
{
    /**
     * @var CartManager
     */
    private $cartManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * CartQuantityForm constructor.
     *
     * @param EntityManager $entityManager
     * @param CartManager   $cartManager
     */
    public function __construct(EntityManager $entityManager, CartManager $cartManager)
    {
        $this->entityManager = $entityManager;
        $this->cartManager = $cartManager;
    }

    /**
     * @return array
     */
    public static function getTranslationMessages()
    {
        return [
            Message::create('constraint.bad_email', 'validators'),
            Message::create("L'email %email% n'est pas un email valide", 'validators'),
        ];
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cart = $this->cartManager->getCurrentCart();
        $builder->setMethod('POST');

        foreach ($cart->getCartProducts() as $cartProduct) {
            $builder->add($cartProduct->getId(), IntegerType::class, [
                'required' => true,
                'data'     => $cartProduct->getQuantity(),
                'attr'     => [
                    'min' => 1,
                ],
            ]);
        }

        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'handleCartChange']);
    }

    /**
     * @param FormEvent $event
     */
    public function handleCartChange(FormEvent $event)
    {
        $form = $event->getForm();
        $fields = $form->getData();

        if ($form->isValid()) {
            $cart = $this->cartManager->getCurrentCart();
            foreach ($fields as $cartProductId => $quantity) {
                $cartProduct = $this->entityManager->getRepository('AlpixelShopBundle:CartProduct')->find($cartProductId);
                if ($cartProduct->getCart() === $cart) {
                    if ($quantity <= 0) {
                        $this->entityManager->remove($cartProduct);
                    } else {
                        $this->cartManager->setNewQuantityToCartProduct($cartProduct, $quantity);
                    }
                }
            }
            $this->entityManager->flush();
        }
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'cart_quantity';
    }
}
