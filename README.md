<p align="center"><a href="https://paystack.com/"><img src="https://raw.githubusercontent.com/PaystackHQ/wordpress-payment-forms-for-paystack/master/icon.png" alt="Paystack Payment for Give"></a></p>

# Paystack Payment for Give

> ⚠️ **IMPORTANT NOTICE**: This repository is no longer actively maintained.
> 
> **Please use the actively maintained fork**: [give-paystack-gateway](https://github.com/impress-org/give-paystack-gateway)
> 
> The fork is maintained by [Impress.org](https://impress.org), the developers of the GiveWP plugin, and serves as the single source of truth going forward. All future development, bug fixes, and support will be handled in the new repository.

Welcome to the Paystack Payment for Give repository on GitHub. Here you can browse the source, look at open issues and keep track of development.

If you are a developer, you can join our Developer Community on [Slack](https://slack.paystack.com).

## Installation

Install the [Paystack Payment for Give](https://wordpress.org/plugins/paystack-for-give/) via the Plugins section of your WordPress Dashboard.


## Running the paystack Give plugin on docker
Contained within this repo, is a dockerfile and a docker-compose file to quickly spin up a wordpress and mysql container with the paystack Give plugin installed.

### Prerequisites
- Install [Docker](https://www.docker.com/)

### Quick Steps
- Create a `local.env` file off the `local.env.sample` in the root directory. Replace the `*******` with the right values
- Run `docker-compose up` from the root directory to build and start the mysql and wordpress containers.
- Visit `localhost:8000` on your browser to access and setup wordpress.
- Run `docker-compose down` from the root directory to stop the containers.


## Documentation
* [Paystack Documentation](https://developers.paystack.co/v1.0/docs/)
* [Paystack Helpdesk](https://paystack.com/help)

## Support
⚠️ **This repository is no longer maintained.** For support, please visit the [actively maintained fork](https://github.com/impress-org/give-paystack-gateway) maintained by Impress.org.

For legacy support related to this archived repository, you can reach out by:

* sending a message from [our website](https://paystack.com/contact).
* posting an issues on the plugin [support forum](https://wordpress.org/support/plugin/paystack-for-give).

## Contributing to Paystack Payment for Give

⚠️ **This repository is no longer maintained.** Please contribute to the [actively maintained fork](https://github.com/impress-org/give-paystack-gateway) maintained by Impress.org.

For legacy information about contributing to this archived repository, you can read our [contributor guidelines](https://github.com/PaystackHQ/wordpress-payment-forms-for-paystack/blob/master/.github/CONTRIBUTING.md).
