#!/usr/bin/python

from tenlayer_listener import TenLayerListener
import urllib2
import syslog
import ConfigParser
import logging

Config = ConfigParser.ConfigParser()
Config.read("/etc/10layer/listener")

server=Config.get("main", "server")

syslog.openlog("10LayerListener")
syslog.syslog('Listener started')

logging.basicConfig(filename='/var/log/10layer.log', format='%(asctime)-6s: %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('10Layer Listener logger')
logger.setLevel(logging.INFO)
logger.info('Listener started')

def delete(message):
	logger.info("Caught message Delete %s %s" % (message[0], message[1]))
	urlStr="%s/workers/eventapi/delete/%s/%s" % (server, message[0], message[1])
	try:
		fh = urllib2.urlopen(urlStr)
		response = fh.read()
		fh.close()
		logger.info("Response: %s" % response)
	except IOError:
		logger.error('Error opening %s' % urlStr)
	
def edit(message):
	logger.info("Caught message Edit %s %s" % (message[0], message[1]))
	urlStr="%s/workers/eventapi/edit/%s/%s" % (server, message[0], message[1])
	try:
		fh = urllib2.urlopen(urlStr)
		response = fh.read()
		fh.close()
		logger.info("Response: %s" % response)
	except IOError:
		logger.error('Error opening %s' % urlStr)

def create(message):
	logger.info("Caught message Create %s %s" % (message[0], message[1]))
	urlStr="%s/workers/eventapi/create/%s/%s" % (server, message[0], message[1])
	try:
		fh = urllib2.urlopen(urlStr)
		response = fh.read()
		fh.close()
		logger.info("Response: %s" % response)
	except IOError:
		logger.error('Error opening %s' % urlStr)

def publish(message):
	logger.info("Caught message Publish %s" % (message))
	urlStr="%s/workers/eventapi/publish/%s" % (server, message)
	try:
		fh = urllib2.urlopen(urlStr)
		response = fh.read()
		fh.close()
		logger.info("Response: %s" % response)
	except IOError:
		logger.error('Error opening %s' % urlStr)
	
listener=TenLayerListener()
listener.add_callback({"callback":delete, "func":"delete"})
listener.add_callback({"callback":edit, "func":"edit"})
listener.add_callback({"callback":create, "func":"create"})
listener.add_callback({"callback":publish, "func":"publish"})
listener.listen()