<?php declare(strict_types=1);

namespace App\PostAction;

use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Class PostActionFactory
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class PostActionFactory
{
    /**
     * @throws \RuntimeException
     */
    public static function fromConfig(array $config): PostActionInterface
    {
        $postActionName = $config[PostActionInterface::KEY_POST_ACTION_NAME];
        $filter = new UnderscoreToCamelCase();
        $class  = \sprintf("%s\\%s", 'App\PostAction', \ucfirst($filter->filter($postActionName)));
        $postAction = new $class($config);

        if (! $postAction instanceof PostActionInterface) {
            throw new \RuntimeException('Invalid post action');
        }

        return $postAction;
    }
}
