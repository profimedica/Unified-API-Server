## Welcome to AJURO Unified API Server

Your compani developed over time multiple servers for multiple clients.

Unfortunately, every API designer implemented a new protocol.

Now you are developing a new client and you have plans for a new API.

But wait! What if you can benefit from all existent APIs !? In all clients, yes.

### The benefits of a Unified API Server

Imagine the benefits of this Unified API Server: 

  Implements client authentification only one time (one language, one entry point);

  Review one single log file for all clients;

  Make data available between clients without touching the underlaying layers;
  
  Ignore bad design practices in underlaying APIs
  
  Update your code once to provide a new functionality for all clients
  
### Best practices to use when developing an API protocol

1) Protocol should NOT be aware of the location, type or structure of storage. Only the resource identifier.

2) Protocol should optionally inform the client on what data can be ignored for better performance. But server will decide over this.

3) Protocol can specify preffered source, api version to use and formatting options.
