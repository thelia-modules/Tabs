<?php

declare(strict_types=1);

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tabs\Controller\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Tabs\Enum\TabItemType;
use Tabs\Enum\TabPositionMode;
use Tabs\Exception\TabNotFoundException;
use Tabs\Form\TabForm;
use Tabs\Service\TabManager;
use Tabs\Tabs;
use Thelia\Controller\Admin\AdminController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\Exception\FormValidationException;

#[Route('/admin/tabs', name: 'tabs.admin.')]
final class TabController extends AdminController
{
    public function __construct(
        private readonly TabManager $tabManager,
    ) {
    }

    #[Route('/save', name: 'save', methods: 'POST')]
    public function save(Request $request, ParserContext $parserContext): Response
    {
        if (null !== $response = $this->checkAuth([], ['Tabs'], AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm(TabForm::getName());

        try {
            $data = $this->validateForm($form)->getData();
            $itemType = TabItemType::from((string) $data['item_type']);
            $itemId = (int) $data['item_id'];
            $tabId = $data['tab_id'] ?? null;

            if (null === $tabId) {
                $this->tabManager->create(
                    $itemType,
                    $itemId,
                    (string) $data['locale'],
                    (string) $data['title'],
                    $data['description'],
                    (bool) $data['visible'],
                );
            } else {
                $this->tabManager->update(
                    (int) $tabId,
                    (string) $data['locale'],
                    (string) $data['title'],
                    $data['description'],
                    (bool) $data['visible'],
                );
            }

            return $this->redirectToItem($itemType, $itemId);
        } catch (FormValidationException $exception) {
            $errorMessage = $this->createStandardFormValidationErrorMessage($exception);
        } catch (TabNotFoundException $exception) {
            $errorMessage = $exception->getMessage();
        } catch (\Exception $exception) {
            $errorMessage = $this->getTranslator()->trans(
                'Sorry, an error occurred: %err',
                ['%err' => $exception->getMessage()],
                Tabs::DOMAIN_NAME,
            );
        }

        $form->setErrorMessage($errorMessage);
        $parserContext
            ->addForm($form)
            ->setGeneralError($errorMessage);

        return $this->redirectToSubmittedItem($request);
    }

    #[Route('/delete', name: 'delete', methods: 'POST')]
    public function delete(Request $request): Response
    {
        if (null !== $response = $this->checkAuth([], ['Tabs'], AccessManager::DELETE)) {
            return $response;
        }

        try {
            $tab = $this->tabManager->delete($request->request->getInt('tab_id'));
        } catch (TabNotFoundException) {
            return $this->pageNotFound();
        }

        return $this->redirectToItem(
            TabItemType::from((string) $tab->getItemType()),
            (int) $tab->getItemId(),
        );
    }

    #[Route('/{tabId}/move/{mode}', name: 'move', requirements: ['tabId' => '\d+', 'mode' => 'up|down'], methods: 'GET')]
    public function move(int $tabId, string $mode): Response
    {
        if (null !== $response = $this->checkAuth([], ['Tabs'], AccessManager::UPDATE)) {
            return $response;
        }

        try {
            $tab = $this->tabManager->move($tabId, TabPositionMode::from($mode));
        } catch (TabNotFoundException) {
            return $this->pageNotFound();
        }

        return $this->redirectToItem(
            TabItemType::from((string) $tab->getItemType()),
            (int) $tab->getItemId(),
        );
    }

    private function redirectToItem(TabItemType $itemType, int $itemId): RedirectResponse
    {
        [$route, $parameters] = $itemType->adminEditRoute($itemId);

        return $this->generateRedirectFromRoute($route, ['current_tab' => 'modules'], $parameters);
    }

    /**
     * Send the admin back to the page they submitted from when the form did not validate.
     */
    private function redirectToSubmittedItem(Request $request): RedirectResponse
    {
        /** @var array<string, mixed> $submitted */
        $submitted = $request->request->all(TabForm::getName());

        $itemType = TabItemType::tryFrom((string) ($submitted['item_type'] ?? ''));
        $itemId = (int) ($submitted['item_id'] ?? 0);

        if (null === $itemType || $itemId <= 0) {
            return $this->generateRedirectFromRoute('admin.home');
        }

        return $this->redirectToItem($itemType, $itemId);
    }
}
