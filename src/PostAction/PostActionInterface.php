<?php declare(strict_types=1);

namespace App\PostAction;

/**
 * Interface PostActionInterface
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
interface PostActionInterface
{
    const KEY_POST_ACTION_NAME = 'post_action_name';

    public function run(): void;
}
