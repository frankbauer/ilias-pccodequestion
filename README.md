# ILIAS Code Editor Page Plugin

**Author**:   Frank Bauer <frank.bauer@fau.de>

**Version**:  2.0.3

**Company**:  Friedrich-Alexander-Universität, Visual Computing

**Supports**: ILIAS 7

## Dependencies
This plugin uses our code-question plugin to perform most of the work. Before installing and activating this plugin, please make sure you have installed https://github.com/frankbauer/ilias-asscodequestion.

## Installation

1. Copy the `pcCodeQuestion` directory to your ILIAS installation at the following path 
(create subdirectories, if necessary):
`Customizing/global/plugins/Services/COPage/PageComponent/pcCodeQuestion`
2. Go to Administration > Plugins
3. Check that you already have installed and activate `assCodeQuestion` (the code-question plugin)
4. Choose **Update** for the `pcCodeQuestion` plugin
5. Choose **Activate** for the `pcCodeQuestion` plugin
6. Choose **Refresh** for the `pcCodeQuestion` plugin languages

There is nothing to configure for this plugin.

## Usage
This plugin is a wrapper for `assCodeQuestion`.  Please refer to https://github.com/frankbauer/ilias-asscodequestion for usage instructions.
  
## Change Log
  
### Version 2.0.0
* Our initial release (we match the Version number of `assCodeQuestion`)

