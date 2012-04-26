# Working with Molajo Triggers #
Triggers are pieces of code that register with the Event Service to fire on specific events.

Registration: Class:Method => Event::Method,
for example EmailTrigger::send() fires on Content::onBeforeDelete

Don't mix up Events (points in time) with Triggers (actions).
todo: design a trigger approach that does not nave OnXYZ in it's code
