<p align="center"><a href="https://paystack.com/"><img src="https://raw.githubusercontent.com/PaystackHQ/wordpress-payment-forms-for-paystack/master/icon.png" alt="Paystack Payment for Give"></a></p>

# Paystack Payment for Give

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
This repository is not suitable for general Paystack support. Please use the issue tracker for bug reports and feature requests directly related to this plugin. For general support, you can reach out by 

* sending a message from [our website](https://paystack.com/contact).
* posting an issues on the plugin [support forum](https://wordpress.org/support/plugin/paystack-for-give).

## Contributing to Paystack Payment for Give

If you have a patch or have stumbled upon an issue with the Paystack Gateway for Paid Membership Pro plugin, you can contribute this back to the code. Please read our [contributor guidelines](https://github.com/PaystackHQ/wordpress-payment-forms-for-paystack/blob/master/.github/CONTRIBUTING.md) for more information how you can do this.