# OAS Tools

[![Join the chat at https://gitter.im/yii2-oas-docs/community](https://badges.gitter.im/yii2-oas-docs/community.svg)](https://gitter.im/yii2-oas-docs/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

![Travis (.org)](https://img.shields.io/travis/danballance/yii2-oas-docs.svg)

Yii2 OAS Docs is an extension for the Yii framework that generates online documentation 
from an Open API Schema.

The extension makes use of the framework-agnostic package [danballance/oas-tools](https://github.com/danballance/oas-tool) for parsing the schema.

Beyond providing a simple means for displaying schemas as web documentation, the primary feature offered is the ability to mix schema generated docs with mark down pages. Often we need to provide long form documentation on the background and usage of an API that wouldn't be appropriate to place within the schema itself. This extension provides a means for combining this documentation with the auto-generated schema docs in a visually consistent manner.

The extension's use does not require any familiarity with the Yii framework. There is a Yii skeleton project [here]() that can be used to quickly install a simple site for getting your schema docs onto the web. At it's simplest all you need to do is drop your schema file into the correct directory and start a web server.

The module has also been designed to be easy to extend. Should you need to modify any of the default behaviours you can load in your own implentations of one or more of the interfaces and control the way the docs are rendered yourself. (More docuemtation on this to follow soon...) 

## Installation

The fastest way to get up and running is to install the Yii skeleton app [here]() with composer - just follow the steps described in the [README]().

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please update the unit tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)