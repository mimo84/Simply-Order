Simply order for Expression Engine
==================================

Simply order is a really simple module for Expression Engine 2.x. Help you to decide the order you would like to dispaly entries in EE.

Actually this version don't works, some edits are needed. According to my engadgement I hope to finish this for September 24,2011.


Contributing
------------

Want to contribute? Great!
This is an EE2 module. 

## Small guide in contributing:

1. Fork it
2. Create a branch (`git checkout -b my_new_branch`)
3. Commit your changes (`git commit -am "Added something"`)
4. Push to the branch (`git push origin my_new_branch`)
5. Create an [Issue][1] with a link to your branch


How to use
----------

# Important note:
   Actually the module has channel 2 on site 1 fixed. If you would like to change, 
   edit the file mcp.simply_order.php at lines 122 and 123:
   (`$data['site_id'] = '1';`)
   (`$data['channel_id'] = '2';`)

1. Download the file, and unzip it into your EE installation following folder's structure.
2. On the EE panel click on  ‘add-ons->modules->simply_order->install’.
3. Once installed click on ‘Simply Order’ in the modules page
4. Insert a new order, with a tag, and then you'll be able to edit that order.

6. In your template use an embed to avoid variables parsing order:
   On the main template:

  {embed="template_group/.embedded_template" order="{exp:simply_order:get}"}

  on the embedded template:

  {exp:channel:entries channel="your_channel" fixed_order="{embed:order}"}

That's all! :)



[Project Homepage][2]

I have developed this module to match my needs. 
Anyway in my freetime I would like to improve it which new features and functionality.


[1]: http://github.com/github/markup/issues
[2]: http://www.zoomingin.net/2011/09/simply-order-for-expression-engine-2-x.html