<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ticketing;

use App\Entity\Ticketing\TicketMerchantProfile;
use App\Entity\User\User;
use App\Enum\RefundPolicyTypeEnum;
use App\Enum\SellerTypeEnum;
use App\Form\Form\Ticketing\TicketMerchantForm;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class TicketMerchantFormValidationTest extends TestCase
{
    private TicketMerchantForm $form;

    #[\Override]
    protected function setUp(): void
    {
        $this->form = new TicketMerchantForm();
    }

    public function testNoRefundsPolicyPassesWithNoExtraFields(): void
    {
        $profile = $this->createProfile(RefundPolicyTypeEnum::NO_REFUNDS);

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())->method('buildViolation');

        $this->form->validateConditionalFields($profile, $context);
    }

    public function testUntilDaysBeforeRequiresDaysBefore(): void
    {
        $profile = $this->createProfile(RefundPolicyTypeEnum::UNTIL_DAYS_BEFORE);
        // No refundPolicyDaysBefore set

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects(self::once())
            ->method('atPath')
            ->with('refundPolicyDaysBefore')
            ->willReturnSelf();
        $violationBuilder->expects(self::once())
            ->method('addViolation');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::once())
            ->method('buildViolation')
            ->with('ticketing.merchant.refund_policy_days_required')
            ->willReturn($violationBuilder);

        $this->form->validateConditionalFields($profile, $context);
    }

    public function testCustomRefundPolicyRequiresText(): void
    {
        $profile = $this->createProfile(RefundPolicyTypeEnum::CUSTOM);
        // No refundPolicyText set

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects(self::once())
            ->method('atPath')
            ->with('refundPolicyText')
            ->willReturnSelf();
        $violationBuilder->expects(self::once())
            ->method('addViolation');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::once())
            ->method('buildViolation')
            ->with('ticketing.merchant.refund_policy_text_required')
            ->willReturn($violationBuilder);

        $this->form->validateConditionalFields($profile, $context);
    }

    public function testValidUntilDaysBeforePolicyWithDays(): void
    {
        $profile = $this->createProfile(RefundPolicyTypeEnum::UNTIL_DAYS_BEFORE);
        $profile->setRefundPolicyDaysBefore(7);

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())->method('buildViolation');

        $this->form->validateConditionalFields($profile, $context);
    }

    public function testValidCustomPolicyWithText(): void
    {
        $profile = $this->createProfile(RefundPolicyTypeEnum::CUSTOM);
        $profile->setRefundPolicyText('Refunds available up to 48 hours before the event.');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())->method('buildViolation');

        $this->form->validateConditionalFields($profile, $context);
    }

    public function testUntilStartPolicyPassesWithNoExtraFields(): void
    {
        $profile = $this->createProfile(RefundPolicyTypeEnum::UNTIL_START);

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::never())->method('buildViolation');

        $this->form->validateConditionalFields($profile, $context);
    }

    private function createProfile(RefundPolicyTypeEnum $refundPolicyType): TicketMerchantProfile
    {
        $user = $this->createMock(User::class);
        $profile = new TicketMerchantProfile($user);
        $profile->setSellerType(SellerTypeEnum::TRADER);
        $profile->setSupportEmail('support@example.com');
        $profile->setRefundPolicyType($refundPolicyType);
        $profile->setTermsAccepted(true);
        $profile->setLawfulEventsCert(true);
        return $profile;
    }
}
