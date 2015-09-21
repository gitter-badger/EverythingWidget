<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
      <title></title>
      <link rel="stylesheet" href="js/xtag/x-tag-components.min.css" />
      <link rel="stylesheet" href="css/grid.css" />
      <link rel="stylesheet" href="css/app.css" />
      <script src="js/xtag/x-tag-components.min.js"></script>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/plugins/CSSPlugin.min.js"></script>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/easing/EasePack.min.js"></script>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/latest/TweenLite.min.js"></script>
      <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
      <script data-main="js/app.js" src="js/require.js"></script>

   </head>
   <body>



      <div class="app-pane container">

         <div class="system-nav-bar row" ></div>

         <div  class="app-nav-bar extend row">
            <button> New </button>
            <button>Edit</button>
            <button onclick="EW.createModal()">Link to somewhere</button>
         </div>
         <div class="app-content row">

            <div class="col xs-3 sm-6 md-3 lg-3">
               <div class="card" >
                  <h1 class="card-title" onclick="EW.createModal(this.parentNode)" >
                     Card Title
                     <p>Card Subtitle</p>
                  </h1>
                  <p class="card-text" >
                     Lorem ipsum nibh eleifend augue tincidunt donec viverra vitae urna est, malesuada tortor suspendisse etiam mollis lorem feugiat enim lacus habitasse, consectetur id ultrices est nostra pretium pellentesque vitae volutpat.
                  </p>
                  <div class="action-row">

                     <div is="ew-menu" class="menu btn success">
                        <h1 class="menu-title">
                           MENU
                        </h1>
                        <ul class="list icon-text">                                            
                           <li>
                              <a href="#item-1">
                                 <i class="list-item-icon"></i>
                                 <h1 class="list-item-title" >
                                    Item 1 with text
                                 </h1>
                              </a>
                           </li>
                           <li>
                              <a href="#item-2">
                                 <i class="list-item-icon"></i>
                                 <h1 class="list-item-title" >
                                    Item 2
                                 </h1>
                              </a>
                           </li>
                           <li>
                              <a href="#item-3">
                                 <i class="list-item-icon"></i>
                                 <h1 class="list-item-title" >
                                    Item 3
                                 </h1>
                              </a>
                           </li>
                           <li>
                              <a href="#item-4">
                                 <i class="list-item-icon"></i>
                                 <h1 class="list-item-title" >
                                    Item 4 new
                                 </h1>
                              </a>
                           </li>
                           <li>
                              <a href="#item-5">
                                 <i class="list-item-icon"></i>
                                 <h1 class="list-item-title" >
                                    Item 5 again new
                                 </h1>
                              </a>
                           </li>
                           <li>
                              <a href="#item-6">
                                 <i class="list-item-icon"></i>
                                 <h1 class="list-item-title" >
                                    Item 6
                                 </h1>
                              </a>
                           </li>
                           <li>
                              <button class="text success" >Action Button</button>         
                           </li>
                        </ul>
                     </div>
                  </div>
                  <div class="action-row">
                     <button class="primary" onclick="EW.createModal()">Call Me</button>
                  </div>
               </div>
            </div>
            <div class="col xs-3 sm-6 md-4 lg-3">
               <div class="card">
                  <h1 class="card-header">
                     Card Header
                     <p>Card Subhead</p>
                  </h1>
                  <div class="action-bar">
                     <button class="text success" onclick="EW.createModal()">Ok</button>
                     <button class="text" onclick="EW.createModal()">Cancel</button>
                  </div>
                  <div class="action-bar">
                     <button class="danger" onclick="EW.createModal()">Delete</button>
                  </div>
                  <p class="subheader" >
                     Sample list:
                  </p>
                  <ul class="list icon-text">                                            
                     <li>
                        <i class="list-item-icon"></i>
                        <h1 class="list-item-title" >
                           Item 1
                        </h1>
                     </li>
                     <li>
                        <i class="list-item-icon"></i>
                        <h1 class="list-item-title" >
                           Item 2
                        </h1>
                     </li>
                     <li>
                        <i class="list-item-icon"></i>
                        <h1 class="list-item-title" >
                           Item 3
                        </h1>
                     </li>
                  </ul>
                  <div class="action-bar">
                     <button class="success" onclick="EW.createModal(this.parentNode)">Confirm</button>
                  </div>
               </div>
            </div>
            <div class="col xs-3 sm-12 md-5 lg-6">
               <div class="card">
                  <h1 class="card-title" onclick="EW.createModal(this)">
                     EW Administration Statistic
                     <p>Update yesterday</p>
                  </h1>
                  <p class="card-text" onclick="EW.createModal(this)">
                     Lorem ipsum nibh eleifend augue tincidunt donec viverra vitae urna est, malesuada tortor suspendisse etiam mollis lorem feugiat enim lacus habitasse, consectetur id ultrices est nostra pretium pellentesque vitae volutpat.
                  </p>
                  <p class="subheader" >
                     Items list
                  </p>
                  <ul class="list with-divider icon-text">
                     <li class="list-item-multiline">
                        <i class="list-item-icon"></i>
                        <h1 class="list-item-title" >
                           Item 1
                        </h1>
                        <p class="list-item-text">
                           Lorem ipsum nibh eleifend augue tincidunt donec viverra vitae urna est
                        </p>
                     </li>
                     <li>
                        <h1 class="list-item-title" >
                           Item 2
                        </h1>
                        <p class="list-item-text">
                           Lorem ipsum nibh eleifend augue tincidunt donec viverra vitae urna est
                        </p>
                        <p class="list-item-text">
                           Lorem ipsum nibh eleifend augue tincidunt donec viverra vitae urna est
                        </p>
                     </li>
                     <li>
                        <i class="list-item-icon"></i>
                        <h1 class="list-item-title" >
                           Item 3
                        </h1>
                     </li>
                     <li>
                        <i class="list-item-icon"></i>
                        <h1 class="list-item-title" >
                           Item 4
                        </h1>
                     </li>
                     <li class="list-item-multiline">
                        <i class="list-item-icon"></i>
                        <h1 class="list-item-title" >
                           Item 5
                        </h1>
                        <p class="list-item-text">
                           Lorem ipsum nibh eleifend augue tincidunt donec viverra vitae urna est
                        </p>
                     </li>
                  </ul>
                  <div class="action-bar">
                     <button onclick="EW.createModal()">refresh</button>
                  </div>
               </div>
            </div>

            <div class="col xs-3 sm-12 md-6 lg-5">
               <div class="card">
                  <h1 class="card-title" onclick="EW.createModal(this)">
                     Employees List
                  </h1>
                  <table class="data-table">
                     <tr>
                        <th>Name</th>
                        <th>E Mail</th>
                        <th>Age</th>
                     </tr>
                     <tr>
                        <td>Eeliya Rasta</td>
                        <td>eeliya.rasta@gmail.com</td>
                        <td>23</td>   
                     </tr>
                     <tr>
                        <td>Someone</td>
                        <td>someone@gmail.com</td>
                        <td>33</td>   
                     </tr>
                     <tr>
                        <td>Sophie Ellis-Bextor</td>
                        <td>  sophieellisbextor.net </td>
                        <td>36</td>   
                     </tr>
                     <tr>
                        <td>Jennifer Lopez</td>
                        <td>  jenniferlopez.com </td>
                        <td>46</td>   
                     </tr>
                     <tr>
                        <td>Robbie Williams</td>
                        <td> robbiewilliams.com </td>
                        <td>41</td>   
                     </tr>
                  </table>
               </div>
            </div>

         </div>
      </div>
   </body>




</html>