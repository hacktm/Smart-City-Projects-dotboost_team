<a href="{SITE_URL}/user/{ACTION}" id="logo">{SITE_NAME}</a>
<ul id="mainMenu" class="clearfix">

			<!-- BEGIN student -->
					<li><a class="{SEL_USER_ACCOUNT}" href="{SITE_URL}/user/account" style="background-image:none;">My account</a>
					<ul style="display:none"></ul>
					</li>
					<li><a class="{SEL_USER_GRADES}" href="{SITE_URL}/user/grades" style="background-image:none;">Grades</a>
					<ul style="display:none"></ul>
					
					</li>
					<li><a class="{SEL_USER_ABSENCE}" href="{SITE_URL}/user/absence" style="background-image:none;">Absences</a>
					<ul style="display:none"></ul>
					</li>
					
					<div class="top_user_menu clearfix">
						<span>Welcome {USERNAME} ({TYPE}) </span>
						<a class="logout" href="{SITE_URL}/user/logout">&nbsp;</a>
					</div>
			<!-- END student -->		
			
			<!-- BEGIN tutor -->
					<li><a class="{SEL_USER_ACCOUNT}" href="{SITE_URL}/user/account" style="background-image:none;">My account</a>
					<ul style="display:none"></ul>
					</li>
					<li><a class="{SEL_USER_GRADES}" href="{SITE_URL}/user/grades" style="background-image:none;">Grades</a>
					<ul style="display:none"></ul>
					</li>
					<li><a class="{SEL_USER_ABSENCE}" href="{SITE_URL}/user/absence" style="background-image:none;">Absences</a>
					<ul style="display:none"></ul>
					</li>
					
					<div class="top_user_menu clearfix">
						<span>Welcome {USERNAME} ({TYPE}) </span>
						<a class="logout" href="{SITE_URL}/user/logout">&nbsp;</a>
					</div>
			<!-- END tutor -->
			
			<!-- BEGIN teacher -->
					<li><a class="{SEL_USER_ACCOUNT}" href="{SITE_URL}/user/account" style="background-image:none;">My account</a>
					<ul style="display:none"></ul>
					</li>
					<li><a class="{SEL_USER_CLASSES}" href="{SITE_URL}/user/classes">Classes</a>
					<ul>
						<li>
							<a class="{SEL_USER_GRADES}" href="{SITE_URL}/user/my-class">My class</a>
						</li>	
						<li>
							<a href="{SITE_URL}/user/view-class">View class</a>
						</li>
					</ul>
					
					
					
					</li>
					<li><a class="{SEL_USER_ABSENCE-EXCUSE}" href="{SITE_URL}/user/absence-excuse" style="background-image:none;">Excuse absences</a>
					<ul style="display:none"></ul>
					</li>
					<li><a class="{SEL_USER_SEND-MESAGE}" href="{SITE_URL}/user/send-message" style="background-image:none;">Send message to tutor</a>
					<ul style="display:none"></ul>
					</li>
					
					<div class="top_user_menu clearfix">
						<span>Welcome {USERNAME} ({TYPE})</span>
						<a class="logout" href="{SITE_URL}/user/logout">&nbsp;</a>
					</div>
					
			<!-- END teacher -->
			<!-- BEGIN account -->
			
			<!-- END account -->	
			
</ul>