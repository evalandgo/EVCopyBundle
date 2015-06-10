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
- @Copy\Simple : Prend la valeur tel quelle et l'ajout à la copie
- @Copy\Variable(name="...") : définir la valeur en fonction des paramètres donnés au Cloner
- @Copy\Entity : copie l'entités
- @Copy\Collection : copie chaque entité de la collection
- @Copy\Construct(variables={"..."}) : donne les bon paramètres au constructeur en fonction des paramètres donnés au Cloner

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

