<?php

declare(strict_types=1);

namespace App\Infrastructure\Article\Security\Symfony;

use App\Domain\Article\Entity\Article;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, ?Article>
 */
class ArticleVoter extends Voter
{
    public const VIEW = 'article_view';
    public const CREATE = 'article_create';
    public const EDIT = 'article_edit';
    public const DELETE = 'article_delete';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::CREATE, self::EDIT, self::DELETE], true)
            && ($subject instanceof Article || $subject === null);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var ?User $user */
        $user = $token->getUser();

        if (!$user) {
            return false;
        }

        if ($this->security->isGranted(UserRole::ADMIN->toSecurityRole())) {
            return true;
        }

        return match ($attribute) {
            self::VIEW => true,
            self::CREATE => $this->security->isGranted(UserRole::AUTHOR->toSecurityRole()),
            self::EDIT, self::DELETE => $this->canEditOrDelete($subject, $user),
            default => false,
        };
    }

    private function canEditOrDelete(?Article $article, User $user): bool
    {
        return $article instanceof Article && $article->getAuthor()->getId() === $user->getId();
    }
}
