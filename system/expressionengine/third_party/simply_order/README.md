Simply order for Expression Engine
==================================

Simply order is a really simple module for Expression Engine 2.x. Help you to decide the order you would like to dispaly entries in EE.


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

1. create a new folder ‘simply_order’ inside /system/expressionengine/third_party/
2. upload simply order files here (whitout Simply-order-for-ExpressionEngine folder)
3. On the EE panel click on  ‘add-ons->modules->simply_order->install’.
4. Once installed click on ‘Simply Order’ in the modules page
5. Insert entry IDs separed by a ‘|’
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