<?php

declare(strict_types=1);

namespace App\Form\Form\Ticketing;

use App\Entity\Ticketing\TicketMerchantProfile;
use App\Enum\RefundPolicyTypeEnum;
use App\Enum\SellerTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TicketMerchantForm extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sellerType', EnumType::class, [
                'class' => SellerTypeEnum::class,
                'label' => 'ticketing.merchant.seller_type',
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('supportEmail', EmailType::class, [
                'label' => 'ticketing.merchant.support_email',
                'required' => true,
                'constraints' => [new NotBlank(), new Email()],
            ])
            ->add('refundPolicyType', EnumType::class, [
                'class' => RefundPolicyTypeEnum::class,
                'label' => 'ticketing.merchant.refund_policy_type',
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('refundPolicyDaysBefore', IntegerType::class, [
                'label' => 'ticketing.merchant.refund_policy_days_before',
                'required' => false,
            ])
            ->add('refundPolicyText', TextareaType::class, [
                'label' => 'ticketing.merchant.refund_policy_text',
                'required' => false,
            ])
            ->add('eventCancellationPolicyText', TextareaType::class, [
                'label' => 'ticketing.merchant.event_cancellation_policy_text',
                'required' => false,
            ])
            ->add('termsAccepted', CheckboxType::class, [
                'label' => 'ticketing.merchant.terms_accepted',
                'constraints' => [new IsTrue(message: 'ticketing.merchant.terms_must_be_accepted')],
            ])
            ->add('lawfulEventsCert', CheckboxType::class, [
                'label' => 'ticketing.merchant.lawful_events_cert',
                'constraints' => [new IsTrue(message: 'ticketing.merchant.lawful_events_cert_required')],
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketMerchantProfile::class,
            'constraints' => [
                new Callback([$this, 'validateConditionalFields']),
            ],
        ]);
    }

    public function validateConditionalFields(TicketMerchantProfile $profile, ExecutionContextInterface $context): void
    {
        if ($profile->getRefundPolicyType() === RefundPolicyTypeEnum::UNTIL_DAYS_BEFORE
            && $profile->getRefundPolicyDaysBefore() === null
        ) {
            $context->buildViolation('ticketing.merchant.refund_policy_days_required')
                ->atPath('refundPolicyDaysBefore')
                ->addViolation();
        }

        if ($profile->getRefundPolicyType() === RefundPolicyTypeEnum::CUSTOM
            && ($profile->getRefundPolicyText() === null || trim($profile->getRefundPolicyText()) === '')
        ) {
            $context->buildViolation('ticketing.merchant.refund_policy_text_required')
                ->atPath('refundPolicyText')
                ->addViolation();
        }
    }
}
