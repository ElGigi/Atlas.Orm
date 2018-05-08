# Mapper Relationships

You can add to the _MapperRelationships_ inside the relevant `define()` method,
calling one of the four available relationship-definition methods:

- `manyToOne($field, $mapperClass)` (aka "belongs to")
- `manyToOneVariant($field, $typeCol)` (aka "polymorphic association")
- `oneToMany($field, $mapperClass)` (aka "has many")
- `oneToOne($field, $mapperClass)` (aka "has one")
- `oneToOneBidi($field, $mapperClass)` for a bidirectional relationship

> Note that many-to-many is not supported as a direct relationship. All
> many-to-many retrievals must occur explicitly through the association mapping
> table, which is what happens at the SQL level anyway.

The `$field` will become a field name on the returned Record object. That field
will be populated from the specified `$mapperClass` in Atlas.

Here is an example:

```php
<?php
namespace App\DataSource\Thread;

use App\DataSource\Author\Author;
use App\DataSource\Summary\Summary;
use App\DataSource\Reply\Reply;
use App\DataSource\Tagging\Tagging;
use App\DataSource\Tag\Tag;
use Atlas\Mapper\MapperRelationships;

class ThreadRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('author', Author::CLASS);
        $this->oneToOne('summary', Summary::CLASS);
        $this->oneToMany('replies', Reply::CLASS);
        $this->oneToMany('taggings', Tagging::CLASS);
    }
}
```

## Relationship Key Columns

By default, in all relationships except many-to-one, the relationship will take
the primary key column(s) in the native table, and map to those same column
names in the foreign table.

In the case of many-to-one, it is the reverse; that is, the relationship will
take the primary key column(s) in the foreign table, and map to those same
column names in the native table.

If you want to use different columns, pass an array of native-to-foreign column
names as the third parameter. For example, if the threads table uses
`author_id`, but the authors table uses just `id`, you can do this:

```php
<?php
class ThreadRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->manyToOne('author', Author::CLASS, [
            // native (threads) column => foreign (authors) column
            'author_id' => 'id',
        ]);
    }
}

```
And on the `oneToMany` side of the relationship, you use the native author table
`id` column with the foreign threads table `author_id` column.

```php
<?php
class AuthorRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->oneToMany('threads', Thread::CLASS, [
            // native (author) column => foreign (threads) column
            'id' => 'author_id',
        ]);
    }
}
```

## Composite Relationship Keys

Likewise, if a table uses a composite key, you can re-map the relationship on
multiple columns. If table `foo` has composite primary key columns of `acol` and
`bcol`, and it maps to table `bar` on `foo_acol` and `foo_bcol`, you would do
this:

```php
<?php
class FooRelationships extends MapperRelationships
{
    protected function define()
    {
        $this->oneToMany('bars', Bar::CLASS, [
            // native (foo) column => foreign (bar) column
            'acol' => 'foo_acol',
            'bcol' => 'foo_bcol',
        ]);
    }
}
```

## Case-Sensitivity

> **Note:**
  This applies only to **string-based** relationship keys. If you are
  using numeric relationship keys, this section does not apply.

Atlas will match records related by string keys in a case-senstive manner. If
your collations on the related string key columns are *not* case sensitive,
Atlas might not match up related records properly in memory after fetching them
from the database. This is because 'foo' and 'FOO' might be equivalent in the
database collation, but they are *not* equivalent in PHP.

In that kind of situation, you will want to tell the relationship to ignore the
case of related string key columns when matching related records. You can do so
with the `ignoreCase()` method on the relationship definition.

```php
<?php
class FooRelationships
{
    protected function define()
    {
        $this->oneToMany('bars', Bar::CLASS)
            ->ignoreCase();
    }
}
```

With that in place, a native value of 'foo' match to a foreign value of 'FOO'
when Atlas is stitching together related records.

## Simple WHERE Conditions

You may find it useful to define simple WHERE conditions on the foreign side of
the relationship. For example, you can handle one side of a many-to-one-variant
(aka "polymorphic association") by selecting only related records of a
particular type.

In the following example, a `comments` table has a `commentable_id` column as
the foreign key value, but is restricted to "video" values on a discriminator
column named `commentable_type`.

```php
class Issue extends Mapper
{
    protected function define()
    {
        $this->oneToMany('comments', Comment::CLASS, [
            'video_id' => 'commentable_id'
        ])->where('commentable_type = ', 'video');
    }
}
```

(These conditions will be honored by `MapperSelect::*joinWith()` as well.)

## Variant Relationships

The many-to-one-variant relationship is somewhat different from the other
relationship types. It is identical to a many-to-one relationship, except that
the relationships vary by a type (or "discriminator") column in the native
table. This allows rows in the native table to "belong to" rows in more than one
foreign table. The typical example is one of comments that can be created on
many different types of content, such as static pages, blog posts, and video
links.

```php
class CommentRelationships extends MapperRelationships
{
    protected function define()
    {
        // The first argument is the field name on the native record;
        // the second argument is the type column on the native table.
        $this->manyToOneVariant('commentable', 'commentable_type')

            // The first argument is the value of the commentable_type column;
            // the second is the related foreign mapper class;
            // the third is the native-to-foreign column mapping.
            ->type('page', Page::CLASS, ['commentable_id' => 'page_id'])
            ->type('post', Post::CLASS, ['commentable_id' => 'post_id'])
            ->type('video', Video::CLASS, ['commentable_id' => 'video_id']);
    }
}
```

Note that there will be one query per variant type in the native record set.
That is, if a native record set (of an arbitrary number of records) refers to a
total of three different variant types, then Atlas will issue three additional
queries to fetch the related records.
