# EVCopyBundle
This is a Symfony2 Bundle helps you to copy an entity with its dependencies

## Features
- Easily configure the copying behavior of entities

## Installation

In composer.json file, add :
```json
{
    "require": {
        "ev/ev-copy-bundle": "dev-master"
    }
}
```

In app/AppKernel.php file, add :
```php
public function registerBundles()
{
    return array(
        // ...
        new EV\CopyBundle\EVCopyBundle(),
        // ...
    );
}
```

## Entity configuration

### Annotations
- **@Copy\Simple** : Takes the value and adding to the copy
- **@Copy\Variable**(name="...") : Set the value based on parameters given to Cloner
- **@Copy\Entity** : Copy the entity
- **@Copy\Collection** : Copy each entity of collection
- **@Copy\Construct**(variables={"..."}) : Gives parameters to the constructor based on parameters given to Cloner

### Example

```php
namespace EV\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EV\CopyBundle\Annotation as Copy;

class Article
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     * @Copy\Variable(name="articleTitle")
     */
    private $title;

    /**
     * @ORM\Column(name="content", type="text")
     * @Copy\Simple
     */
    private $content;

    /**
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity="EV\BlogBundle\Entity\Options", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="optionsId", referencedColumnName="id")
     * @Copy\Entity
     */
    private $options;

    /**
     * @ORM\ManyToOne(targetEntity="EV\BlogBundle\Entity\Author", inversedBy="articles")
     * @ORM\JoinColumn(name="authorId", referencedColumnName="id", nullable=false)
     * @Copy\Simple
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="EV\BlogBundle\Entity\Comment", mappedBy="article", cascade={"persist"})
     * @Copy\Collection
     */
    private $comments

    /**
     * @ORM\ManyToOne(targetEntity="EV\BlogBundle\Entity\Blog", inversedBy="articles")
     * @ORM\JoinColumn(name="blogId", referencedColumnName="id", onDelete="cascade")
     */
    protected $blog;

    /**
     * @Copy\Construct(variables={"blog"})
     */
    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
        $this->date = new \DateTime('now');
    }

    // Getters, Setters and Adders methods...

    public function addComment(\EV\BlogBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        // IMPORTANT : without this line, the copy won't work
        $comment->setArticle($this);

        return $this;
    }

}
```

```php
namespace EV\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EV\CopyBundle\Annotation as Copy;

class Comment
{

    /**
     * @ORM\Column(name="pseudo", type="string", length=255)
     * @Copy\Simple
     */
    $pseudo;

    /**
     * @ORM\Column(name="content", type="text")
     * @Copy\Simple
     */
    $content;

    /**
     * @ORM\ManyToOne(targetEntity="EV\BlogBundle\Entity\Article", inversedBy="comments")
     * @ORM\JoinColumn(name="articleId", referencedColumnName="id", nullable=false)
     */
    protected $article;

    // Getters, Setters and Adders methods...

}
```

## Usage example

```php

public function articleCopyAction() {

    //...

    $params = array(
        'blog' => $blog,
        'articleTitle' => $article->getTitle().' - Copy'
    );

    $articleCopy = $this->get('ev_copy.factory.cloner')->copy($article, $params);

    //...

}
```

## How to contribute
To contribute just open a Pull Request with your new code taking into account that if you add new features or modify existing ones you have to document in this README what they do.

## License
EVCopyBundle is licensed under [MIT](https://github.com/evalandgo/EVCopyBundle/blob/master/LICENSE)

