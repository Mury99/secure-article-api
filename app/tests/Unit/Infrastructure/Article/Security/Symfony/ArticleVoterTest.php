<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Article\Security\Symfony;

use App\Domain\Article\Entity\Article;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\UserRole;
use App\Infrastructure\Article\Security\Symfony\ArticleVoter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ArticleVoterTest extends TestCase
{
    private MockObject $security;
    private TokenInterface&MockObject $token;
    private User&MockObject $user;

    private ArticleVoter $voter;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->token = $this->createMock(TokenInterface::class);

        $this->voter = new ArticleVoter($this->security);

        $this->user = $this->createMock(User::class);
        $this->token->method('getUser')->willReturn($this->user);
    }

    #[DataProvider('provideScenarios')]
    public function testVote(UserRole $grantedRole, string $attribute, bool $isAuthor, bool $expected): void
    {
        $this->security->expects($this->atMost(2))
            ->method('isGranted')
            ->willReturnCallback(
                fn (string $role) => $role === $grantedRole->toSecurityRole()
            );

        $article = in_array($attribute, [ArticleVoter::EDIT, ArticleVoter::DELETE], true)
            ? $this->createArticleWithAuthor($isAuthor)
            : null;

        $result = $this->voter->vote($this->token, $article, [$attribute]);

        $this->assertSame(
            $expected,
            $result > 0,
            sprintf(
                'Expected %s to be %s for role %s (author: %s)',
                $attribute,
                $expected ? 'GRANTED' : 'DENIED',
                $grantedRole->toSecurityRole(),
                $isAuthor ? 'yes' : 'no'
            )
        );
    }

    /**
     * @return iterable<array{UserRole, string, bool, bool}>
     */
    public static function provideScenarios(): iterable
    {
        return [
            // Admin
            [UserRole::ADMIN, ArticleVoter::VIEW, false, true],
            [UserRole::ADMIN, ArticleVoter::CREATE, false, true],
            [UserRole::ADMIN, ArticleVoter::EDIT, false, true],
            [UserRole::ADMIN, ArticleVoter::DELETE, false, true],

            // Author
            [UserRole::AUTHOR, ArticleVoter::VIEW, false, true],
            [UserRole::AUTHOR, ArticleVoter::CREATE, false, true],
            [UserRole::AUTHOR, ArticleVoter::EDIT, true, true],
            [UserRole::AUTHOR, ArticleVoter::EDIT, false, false],
            [UserRole::AUTHOR, ArticleVoter::DELETE, true, true],
            [UserRole::AUTHOR, ArticleVoter::DELETE, false, false],

            // Reader
            [UserRole::READER, ArticleVoter::VIEW, false, true],
            [UserRole::READER, ArticleVoter::CREATE, false, false],

            // Default role
            [UserRole::USER, ArticleVoter::VIEW, false, true],
            [UserRole::USER, ArticleVoter::CREATE, false, false],
            [UserRole::USER, ArticleVoter::EDIT, true, true],
            [UserRole::USER, ArticleVoter::EDIT, false, false],
            [UserRole::USER, ArticleVoter::DELETE, true, true],
            [UserRole::USER, ArticleVoter::DELETE, false, false],
        ];
    }

    private function createArticleWithAuthor(bool $isAuthor): Article
    {
        $article = $this->createMock(Article::class);
        $author = $this->createMock(User::class);

        $userId = Uuid::fromString('018fa9c5-1aa3-79be-bc3f-6b327a3ee46b');
        $authorId = $isAuthor ? $userId : Uuid::fromString('018fa9c5-1aa3-79be-bc3f-6b327a3ee46c');

        $author->method('getId')->willReturn($authorId);
        $this->user->method('getId')->willReturn($userId);

        $article->method('getAuthor')->willReturn($author);

        return $article;
    }
}
