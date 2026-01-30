# Drupal CMS

Drupal CMS is a fast-moving open source product that enables site builders to easily create new Drupal sites and extend them with smart defaults, all using their browser.

## Getting started

### Using this repo with Lando
Note: These instructions are not tested, they're just, educated guesses by Alison.

1. Clone this repo.
2. Run `lando start`
3. Install Drupal:
   1. To use the Starter site template:
    ```
    lando drush site:install --db-url=mysql://drupal11:drupal11@database/drupal11 -y
    ```
   2. To use the Byte site template, a big fancy site template (i.e. lots of features and content, like Umami):
    ```
    lando drush site:install recipes/byte --db-url=mysql://drupal11:drupal11@database/drupal11 -y
    ```
4. Go to your new site!

P.S. Byte didn't work for me must now, oh well.

### Starting from scratch
i.e. what I did to get to this point / this repo -- it's a mash-up of these ["testing non-stable Drupal CMS with ddev" instructions](https://www.drupal.org/project/cms/releases/2.0.0-alpha2) (Jan 20, 2026: I don't see the instructions I'm alluding to...) and [these Drupal CMS + lando instructions](https://docs.lando.dev/plugins/drupal/guides/drupal-cms.html):

1. Start with lando "TLDR quick install" at the top of [that lando doc](https://docs.lando.dev/plugins/drupal/guides/drupal-cms.html).  🛑 Stop after `lando start`.
2. Create project:
  ```
  lando composer create-project drupal/cms:2-alpha2 tmp -s alpha --no-install && cp -r tmp/. . && rm -rf tmp
  ```
3. More composer commands based on instructions in the [CMS alpha2 release notes](https://www.drupal.org/project/cms/releases/2.0.0-alpha2):
  ```
  lando composer config minimum-stability alpha
  lando composer require --no-update drupal/drupal_cms_media:@dev
  lando composer install
  ```
4. Install Drupal -- see above, step 3. Only reason I mention it here is, technically this repo contains a `settings.php` file that was created when I installed Drupal.

### Drupal CMS 2.0.0 stable

From when I did all this ^^ but with CMS 2 stable release that came out on Jan 28, 2026...
1. Start with lando "TLDR quick install" at the top of [that lando doc](https://docs.lando.dev/plugins/drupal/guides/drupal-cms.html).  🛑 Stop after `lando start`.
2. Create project + composer install:
  ```
  lando composer create-project drupal/cms tmp --no-install && cp -r tmp/. . && rm -rf tmp
  lando composer install
  ```
3. Install Drupal -- see ["Using this repo with Lando" above](https://github.com/alisonjo315/drupal-cms-2/blob/main/README.md#using-this-repo-with-lando), step 3.
    * FYI: This time, Byte worked fine for me.


<hr>

_**📚 Note: The rest of this README is what came with the project.**_

<hr>

Drupal CMS has the same system requirements as Drupal core, so you can use your preferred setup to run it locally. [See the Drupal User Guide for more information](https://www.drupal.org/docs/user_guide/en/installation-chapter.html) on how to set up Drupal.

### Installation options

The Drupal CMS installer offers a list of features preconfigured with smart defaults. You will be able to customize whatever you choose, and add additional features, once you are logged in.

After the installer is complete, you will land on the dashboard.

## Documentation

Coming soon ... [We're working on Drupal CMS specific documentation](https://www.drupal.org/project/drupal_cms/issues/3454527).

In the meantime, learn more about managing a Drupal-based application in the [Drupal User Guide](https://www.drupal.org/docs/user_guide/en/index.html).

## Contributing

Drupal CMS is developed in the open on [Drupal.org](https://www.drupal.org). We are grateful to the community for reporting bugs and contributing fixes and improvements.

[Report issues in the queue](https://drupal.org/node/add/project-issue/drupal_cms), providing as much detail as you can. You can also join the #drupal-cms-support channel in the [Drupal Slack community](https://www.drupal.org/slack).

Drupal CMS has adopted a [code of conduct](https://www.drupal.org/dcoc) that we expect all participants to adhere to.

To contribute to Drupal CMS development, see the [drupal_cms project](https://www.drupal.org/project/drupal_cms).

## License

Drupal CMS and all derivative works are licensed under the [GNU General Public License, version 2 or later](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

Learn about the [Drupal trademark and logo policy here](https://www.drupal.com/trademark).
