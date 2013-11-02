<h1>Syntax</h1>
<p>Gid (“Get it done.”) uses <a href="http://todotxt.com/" target="blank"><code>todo.txt</code></a> Syntax (Learn more about the Syntax <a href="https://github.com/ginatrapani/todo.txt-cli/wiki/The-Todo.txt-Format" target="blank">here</a>) - an easy to use text based standard for todo list formatting. This page describes how to format todos so Gid recognizes your priorities, projects, contexts and deadlines.</p>
<h2>Basic usage</h2>
<pre>
(A) 2013-12-14 08:16:37 finish @presentation of +someProject for +someCustomer DUE:2013-12-22 09:00
</pre>
<dl class="dl-horizontal">
	<dt>(A)</dt>
	<dd><b>Priority</b><br />The priority is an uppercase letter from A to Z, starting with A for highest and ending with Z for lowest priority, surrounded by parentheses. The priority must be at the beginning of your todo, otherwise it wont be recognized.<br />If recognized, priorities look like this: <span class="label label-danger">A</span></dd>
	<dt>2013-12-14 08:16:37</dt>
	<dd><b>Creation date</b><br />The date your todo is created. You can skip this, Gid will create it automatically. The creation date must be at the beginning of a todo, after the priority if given. Gid understands the following date/time formats: <code>2013-12-14</code>, <code>2013-12-14 08:16</code> and <code>2013-12-14 08:16:37</code>.<br />If recognized, creation dates show up in their column on the right of a list.</dd>
	<dt>+someProject,<br />+someCustomer</dt>
	<dd><b>Project</b><br />Projects start with a + sign and are used to organize your todos like categories or folders. You can use multiple projects in one todo. To connect todos across projects use contexts.<br />If recognized, projects look like this: <span class="label label-info">Projectname</span></dd>
	<dt>@presentation</dt>
	<dd><b>Context</b><br />Contexts start wit an @ sign, they may contain letters and numbers, no spaces. You can have multiple contexts in a todo. Contexts are used across todos to connect contexts which are not particularly bound to single projects like @call, @buy, @email. For example if you want to see all the calls you have to make, you do not want to search them seperatly in every project, you need all todos needing a call at once. In the example presentation is set as context, because there might be other projects with presentations to.<br />If recognized, contexts look like this: <span class="label label-warning">Contextname</span></dd>
	<dt>DUE:2013-12-22 09:00</dt>
	<dd><b>Deadline</b><br />Date/time until this todo has to be finished. Gid understands following DUE formats: <code>DUE:2013-12-22</code>, <code>DUE:2013-12-22 09:00</code> and <code>DUE:2013-12-22 09:00:00</code><br />If recognized, deadlines look like this: <span class="label label-danger">2013-12-22</span> <span class="label label-danger">2013-12-22 09:00</span> <span class="label label-danger">2013-12-22 09:00:00</span></dd>
</dl>
<h2>Complete tasks</h2>
<dl class="dl-horizontal">
	<dt>DONE:2013-12-22 09:00</dt>
	<dd>Completed tasks contain a DONE:date/time tag for now, this will change in the future. Completed tasks will start with a lowercase x letter followed by date/time of completion.<br />If recognized, task completion dates look like this: <span class="label label-default">2013-12-22</span> <span class="label label-default">2013-12-22 09:00</span> <span class="label label-default">2013-12-22 09:00:00</span></dd>
</dl>
