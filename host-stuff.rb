require 'socket'
ip = IPSocket.getaddress(Socket.gethostname)
#hostname = Socket.gethostbyname(Socket.gethostname).first
#puts hostname
#puts ip
#print last octet
puts ip.split('.')[3]