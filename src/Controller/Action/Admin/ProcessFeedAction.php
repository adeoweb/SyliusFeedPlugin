<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Controller\Action\Admin;

use Setono\SyliusFeedPlugin\Message\Command\ProcessFeed;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

/**
 * @psalm-suppress UndefinedClass
 * @psalm-suppress MixedArgument
 * @psalm-suppress UndefinedInterfaceMethod
 * @psalm-suppress MixedAssignment
 */
final class ProcessFeedAction
{
    private MessageBusInterface $commandBus;

    private UrlGeneratorInterface $urlGenerator;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    public function __construct(
        MessageBusInterface $commandBus,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        TranslatorInterface $translator,
    ) {
        $this->commandBus = $commandBus;
        $this->urlGenerator = $urlGenerator;
        $session = $requestStack->getSession();
        $this->flashBag = $session->getFlashBag();
        $this->translator = $translator;
    }

    public function __invoke(int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new ProcessFeed($id));

        $this->flashBag->add('success', $this->translator->trans('setono_sylius_feed.feed_generation_triggered'));

        return new RedirectResponse($this->urlGenerator->generate('setono_sylius_feed_admin_feed_show', ['id' => $id]));
    }
}
