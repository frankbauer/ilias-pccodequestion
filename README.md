# ILIAS Code Editor Page Plugin

**Author**:   Frank Bauer <frank.bauer@fau.de>

**Version**:  2.0.3

**Company**:  Friedrich-Alexander-Universität, Visual Computing

**Supports**: ILIAS 9

## Dependencies
This plugin uses our code-question plugin to perform most of the work. Before installing and activating this plugin, please make sure you have installed https://github.com/frankbauer/ilias-asscodequestion.

## Installation

1. Copy the `pcCodeQuestion` directory to your ILIAS installation at the following path 
(create subdirectories, if necessary):
`Customizing/global/plugins/Services/COPage/PageComponent/pcCodeQuestion`
2. You need to update the classmap after installing any Plugin. In the folder of your ILIAS installation, call `composer install --no-dev` to  regenerate the class map and build the static artifacts map. 
3. Go to Administration > Plugins. If you do not see the plugin, your static artifact map needs to be rebuilt. You can rebuild those by calling `php setup/cli.php build-artifacts` in the folder of your ILIAS installation.
4. Check that you already have installed and activate `assCodeQuestion` (the code-question plugin)
5. Choose **Update** for the `pcCodeQuestion` plugin
6. Choose **Activate** for the `pcCodeQuestion` plugin
7. Choose **Refresh** for the `pcCodeQuestion` plugin languages

There is nothing to configure for this plugin.

## Usage
This plugin is a wrapper for `assCodeQuestion`.  Please refer to https://github.com/frankbauer/ilias-asscodequestion for usage instructions.
  
## Change Log
  
### Version 2.0.0
* Our initial release (we match the Version number of `assCodeQuestion`)

