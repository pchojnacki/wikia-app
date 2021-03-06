at_exit do
  $browser.close unless ENV['KEEP_BROWSER_OPEN'] == 'true'
end

#This is for the Headings test
Before('@edit_user_page') do
  if (!$edit_user_page or !(ENV['REUSE_BROWSER'] == 'true')) and @browser
    step 'I am logged in'
    step 'I am at my user page'
    step 'I edit the page with Editing with'
    $edit_user_page=true
  end
end

#This is for the bullets, indent-outdent, and the General Markup tests
Before('@make_selectable_line') do
  if (!$make_selectable_line or !(ENV['REUSE_BROWSER'] == 'true')) and @browser
    step 'I am logged in'
    step 'I am at my user page'
    step 'I click Edit for VisualEditor'
    step 'I type in an input string'
    step 'select the string'
    $make_selectable_line=true
  end
end
