<?php


namespace Filehosting\Validators;

use Filehosting\Exceptions\CommentAdditionException;

class CommentValidator
{
    public function validate (\Filehosting\Models\Comment $comment) :array
    {
        $errors=[];
        if (mb_strlen($comment->author) < 3 || $comment->author==null) { // checks a comment's author name
            $errors[]='Имя автора не может быть короче трех знаков';
        }
        if (mb_strlen($comment->text)>1000 || $comment->text==null) { // checks a comment's text
            $errors[]='Ваш комментарий слишком длинный. Сократите его до 1000 знаков (это примерно полстраницы А4)';
        }

        if (!($comment->parent_id==null || (is_int($comment->parent_id)&& $comment->parent_id>0 ))) {
            throw new CommentAdditionException('Неправильный формат parent id');
        } // вот тут надо пофиксить

        if(!preg_match('/^[0-9]{3}(.[0-9]{3})*$/',$comment->matpath)) {
            throw new CommentAdditionException('Неправильный формат matpath');
        }
        return $errors;
    }
}