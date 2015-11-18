<?php
/**
 * @author Joas Schilling <nickvergessen@owncloud.com>
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace Test\Notification;


use OC\Notification\Notification;
use OC\Notification\INotification;
use Test\TestCase;

class NotificationTest extends TestCase {
	/** @var INotification */
	protected $notification;

	public function setUp() {
		parent::setUp();
		$this->notification = new Notification();
	}

	protected function dataValidString($maxLength) {
		$dataSets = [
			['test1'],
			[str_repeat('a', 1)],
		];
		if ($maxLength !== false) {
			$dataSets[] = [str_repeat('a', $maxLength)];
		}
		return $dataSets;
	}

	protected function dataInvalidString($maxLength) {
		$dataSets = [
			[true],
			[false],
			[0],
			[1],
			[''],
			[[]],
		];
		if ($maxLength !== false) {
			$dataSets[] = [str_repeat('a', $maxLength + 1)];
			$dataSets[] = [[str_repeat('a', $maxLength + 1)]];
		}
		return $dataSets;
	}

	protected function dataValidInt() {
		return [
			[0],
			[1],
			[time()],
		];
	}

	protected function dataInvalidInt() {
		return [
			[true],
			[false],
			[''],
			['a'],
			[str_repeat('a', 256)],
			[[]],
			[['a']],
			[[str_repeat('a', 256)]],
		];
	}

	public function dataSetApp() {
		return $this->dataValidString(32);
	}

	/**
	 * @dataProvider dataSetApp
	 * @param string $app
	 */
	public function testSetApp($app) {
		$this->assertSame('', $this->notification->getApp());
		$this->assertSame($this->notification, $this->notification->setApp($app));
		$this->assertSame($app, $this->notification->getApp());
	}

	public function dataSetAppInvalid() {
		return $this->dataInvalidString(32);
	}

	/**
	 * @dataProvider dataSetAppInvalid
	 * @param mixed $app
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetAppInvalid($app) {
		$this->notification->setApp($app);
	}

	public function dataSetUser() {
		return $this->dataValidString(64);
	}

	/**
	 * @dataProvider dataSetUser
	 * @param string $user
	 */
	public function testSetUser($user) {
		$this->assertSame('', $this->notification->getUser());
		$this->assertSame($this->notification, $this->notification->setUser($user));
		$this->assertSame($user, $this->notification->getUser());
	}

	public function dataSetUserInvalid() {
		return $this->dataInvalidString(64);
	}

	/**
	 * @dataProvider dataSetUserInvalid
	 * @param mixed $user
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetUserInvalid($user) {
		$this->notification->setUser($user);
	}

	public function dataSetTimestamp() {
		return $this->dataValidInt();
	}

	/**
	 * @dataProvider dataSetTimestamp
	 * @param int $timestamp
	 */
	public function testSetTimestamp($timestamp) {
		$this->assertSame(0, $this->notification->getTimestamp());
		$this->assertSame($this->notification, $this->notification->setTimestamp($timestamp));
		$this->assertSame($timestamp, $this->notification->getTimestamp());
	}

	public function dataSetTimestampInvalid() {
		return $this->dataInvalidInt();
	}

	/**
	 * @dataProvider dataSetTimestampInvalid
	 * @param mixed $timestamp
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetTimestampInvalid($timestamp) {
		$this->notification->setTimestamp($timestamp);
	}

	public function dataSetObject() {
		return [
			['a', 1],
			[str_repeat('a', 64), time()],
		];
	}

	/**
	 * @dataProvider dataSetObject
	 * @param string $type
	 * @param int $id
	 */
	public function testSetObject($type, $id) {
		$this->assertSame('', $this->notification->getObjectType());
		$this->assertSame(0, $this->notification->getObjectId());
		$this->assertSame($this->notification, $this->notification->setObject($type, $id));
		$this->assertSame($type, $this->notification->getObjectType());
		$this->assertSame($id, $this->notification->getObjectId());
	}

	public function dataSetObjectTypeInvalid() {
		return $this->dataInvalidString(64);
	}

	/**
	 * @dataProvider dataSetObjectTypeInvalid
	 * @param mixed $type
	 *
	 * @expectedException \InvalidArgumentException
	 * @expectedMessage 'The given object type is invalid'
	 */
	public function testSetObjectTypeInvalid($type) {
		$this->notification->setObject($type, 1337);
	}

	public function dataSetObjectIdInvalid() {
		return $this->dataInvalidInt();
	}

	/**
	 * @dataProvider dataSetObjectIdInvalid
	 * @param mixed $id
	 *
	 * @expectedException \InvalidArgumentException
	 * @expectedMessage 'The given object id is invalid'
	 */
	public function testSetObjectIdInvalid($id) {
		$this->notification->setObject('object', $id);
	}

	public function dataSetSubject() {
		return [
			['a', []],
			[str_repeat('a', 64), [str_repeat('a', 160)]],
			[str_repeat('a', 64), array_fill(0, 160, 'a')],
		];
	}

	/**
	 * @dataProvider dataSetSubject
	 * @param string $subject
	 * @param array $parameters
	 */
	public function testSetSubject($subject, $parameters) {
		$this->assertSame('', $this->notification->getSubject());
		$this->assertSame([], $this->notification->getSubjectParameters());
		$this->assertSame($this->notification, $this->notification->setSubject($subject, $parameters));
		$this->assertSame($subject, $this->notification->getSubject());
		$this->assertSame($parameters, $this->notification->getSubjectParameters());
	}

	public function dataSetSubjectInvalidSubject() {
		return $this->dataInvalidString(64);
	}

	/**
	 * @dataProvider dataSetSubjectInvalidSubject
	 * @param mixed $subject
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetSubjectInvalidSubject($subject) {
		$this->notification->setSubject($subject, []);
	}

	public function dataSetParsedSubject() {
		return $this->dataValidString(false);
	}

	/**
	 * @dataProvider dataSetParsedSubject
	 * @param string $subject
	 */
	public function testSetParsedSubject($subject) {
		$this->assertSame('', $this->notification->getParsedSubject());
		$this->assertSame($this->notification, $this->notification->setParsedSubject($subject));
		$this->assertSame($subject, $this->notification->getParsedSubject());
	}

	public function dataSetParsedSubjectInvalid() {
		return $this->dataInvalidString(false);
	}

	/**
	 * @dataProvider dataSetParsedSubjectInvalid
	 * @param mixed $subject
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetParsedSubjectInvalid($subject) {
		$this->notification->setParsedSubject($subject);
	}

	public function dataSetMessage() {
		return [
			['a', []],
			[str_repeat('a', 64), [str_repeat('a', 160)]],
			[str_repeat('a', 64), array_fill(0, 160, 'a')],
		];
	}

	/**
	 * @dataProvider dataSetMessage
	 * @param string $message
	 * @param array $parameters
	 */
	public function testSetMessage($message, $parameters) {
		$this->assertSame('', $this->notification->getMessage());
		$this->assertSame([], $this->notification->getMessageParameters());
		$this->assertSame($this->notification, $this->notification->setMessage($message, $parameters));
		$this->assertSame($message, $this->notification->getMessage());
		$this->assertSame($parameters, $this->notification->getMessageParameters());
	}

	public function dataSetMessageInvalidMessage() {
		return $this->dataInvalidString(64);
	}

	/**
	 * @dataProvider dataSetMessageInvalidMessage
	 * @param mixed $message
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetMessageInvalidMessage($message) {
		$this->notification->setMessage($message, []);
	}

	public function dataSetParsedMessage() {
		return $this->dataValidString(false);
	}

	/**
	 * @dataProvider dataSetParsedMessage
	 * @param string $message
	 */
	public function testSetParsedMessage($message) {
		$this->assertSame('', $this->notification->getParsedMessage());
		$this->assertSame($this->notification, $this->notification->setParsedMessage($message));
		$this->assertSame($message, $this->notification->getParsedMessage());
	}

	public function dataSetParsedMessageInvalid() {
		return $this->dataInvalidString(false);
	}

	/**
	 * @dataProvider dataSetParsedMessageInvalid
	 * @param mixed $message
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetParsedMessageInvalid($message) {
		$this->notification->setParsedMessage($message);
	}

	public function dataSetLink() {
		return $this->dataValidString(4000);
	}

	/**
	 * @dataProvider dataSetLink
	 * @param string $link
	 */
	public function testSetLink($link) {
		$this->assertSame('', $this->notification->getLink());
		$this->assertSame($this->notification, $this->notification->setLink($link));
		$this->assertSame($link, $this->notification->getLink());
	}

	public function dataSetLinkInvalid() {
		return $this->dataInvalidString(4000);
	}

	/**
	 * @dataProvider dataSetLinkInvalid
	 * @param mixed $link
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetLinkInvalid($link) {
		$this->notification->setLink($link);
	}

	public function testCreateAction() {
		$action = $this->notification->createAction();
		$this->assertInstanceOf('OC\Notification\IAction', $action);
	}

	public function testAddAction() {
		/** @var \OC\Notification\IAction|\PHPUnit_Framework_MockObject_MockObject $action */
		$action = $this->getMockBuilder('OC\Notification\IAction')
			->disableOriginalConstructor()
			->getMock();
		$action->expects($this->once())
			->method('isValid')
			->willReturn(true);
		$action->expects($this->never())
			->method('isValidParsed');

		$this->assertSame($this->notification, $this->notification->addAction($action));

		$this->assertEquals([$action], $this->notification->getActions());
		$this->assertEquals([], $this->notification->getParsedActions());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testAddActionInvalid() {
		/** @var \OC\Notification\IAction|\PHPUnit_Framework_MockObject_MockObject $action */
		$action = $this->getMockBuilder('OC\Notification\IAction')
			->disableOriginalConstructor()
			->getMock();
		$action->expects($this->once())
			->method('isValid')
			->willReturn(false);
		$action->expects($this->never())
			->method('isValidParsed');

		$this->notification->addAction($action);
	}

	public function testAddActionSecondPrimary() {
		/** @var \OC\Notification\IAction|\PHPUnit_Framework_MockObject_MockObject $action */
		$action = $this->getMockBuilder('OC\Notification\IAction')
			->disableOriginalConstructor()
			->getMock();
		$action->expects($this->exactly(2))
			->method('isValid')
			->willReturn(true);
		$action->expects($this->exactly(2))
			->method('isPrimary')
			->willReturn(true);

		$this->assertSame($this->notification, $this->notification->addAction($action));

		$this->setExpectedException('\InvalidArgumentException');
		$this->notification->addAction($action);
	}

	public function testAddParsedAction() {
		/** @var \OC\Notification\IAction|\PHPUnit_Framework_MockObject_MockObject $action */
		$action = $this->getMockBuilder('OC\Notification\IAction')
			->disableOriginalConstructor()
			->getMock();
		$action->expects($this->once())
			->method('isValidParsed')
			->willReturn(true);
		$action->expects($this->never())
			->method('isValid');

		$this->assertSame($this->notification, $this->notification->addParsedAction($action));

		$this->assertEquals([$action], $this->notification->getParsedActions());
		$this->assertEquals([], $this->notification->getActions());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testAddParsedActionInvalid() {
		/** @var \OC\Notification\IAction|\PHPUnit_Framework_MockObject_MockObject $action */
		$action = $this->getMockBuilder('OC\Notification\IAction')
			->disableOriginalConstructor()
			->getMock();
		$action->expects($this->once())
			->method('isValidParsed')
			->willReturn(false);
		$action->expects($this->never())
			->method('isValid');

		$this->notification->addParsedAction($action);
	}

	public function testAddActionSecondParsedPrimary() {
		/** @var \OC\Notification\IAction|\PHPUnit_Framework_MockObject_MockObject $action */
		$action = $this->getMockBuilder('OC\Notification\IAction')
			->disableOriginalConstructor()
			->getMock();
		$action->expects($this->exactly(2))
			->method('isValidParsed')
			->willReturn(true);
		$action->expects($this->exactly(2))
			->method('isPrimary')
			->willReturn(true);

		$this->assertSame($this->notification, $this->notification->addParsedAction($action));

		$this->setExpectedException('\InvalidArgumentException');
		$this->notification->addParsedAction($action);
	}

	public function dataIsValid() {
		return [
			[false, '', false],
			[true, '', false],
			[false, 'a', false],
			[true, 'a', true],
		];
	}

	/**
	 * @dataProvider dataIsValid
	 *
	 * @param bool $isValidCommon
	 * @param string $subject
	 * @param bool $expected
	 */
	public function testIsValid($isValidCommon, $subject, $expected) {
		/** @var \OC\Notification\INotification|\PHPUnit_Framework_MockObject_MockObject $notification */
		$notification = $this->getMockBuilder('\OC\Notification\Notification')
			->setMethods([
				'isValidCommon',
				'getSubject',
			])
			->getMock();

		$notification->expects($this->once())
			->method('isValidCommon')
			->willReturn($isValidCommon);

		$notification->expects(!$isValidCommon ? $this->never() : $this->once())
			->method('getSubject')
			->willReturn($subject);

		$notification->expects($this->never())
			->method('getParsedSubject')
			->willReturn($subject);

		$this->assertEquals($expected, $notification->isValid());
	}

	/**
	 * @dataProvider dataIsValid
	 *
	 * @param bool $isValidCommon
	 * @param string $subject
	 * @param bool $expected
	 */
	public function testIsParsedValid($isValidCommon, $subject, $expected) {
		/** @var \OC\Notification\INotification|\PHPUnit_Framework_MockObject_MockObject $notification */
		$notification = $this->getMockBuilder('\OC\Notification\Notification')
			->setMethods([
				'isValidCommon',
				'getParsedSubject',
			])
			->getMock();

		$notification->expects($this->once())
			->method('isValidCommon')
			->willReturn($isValidCommon);

		$notification->expects(!$isValidCommon ? $this->never() : $this->once())
			->method('getParsedSubject')
			->willReturn($subject);

		$notification->expects($this->never())
			->method('getSubject')
			->willReturn($subject);

		$this->assertEquals($expected, $notification->isValidParsed());
	}

	public function dataIsValidCommon() {
		return [
			['', '', 0, '', 0, false],
			['app', '', 0, '', 0, false],
			['app', 'user', 0, '', 0, false],
			['app', 'user', time(), '', 0, false],
			['app', 'user', time(), 'type', 0, false],
			['app', 'user', time(), 'type', 42, true],
		];
	}

	/**
	 * @dataProvider dataIsValidCommon
	 *
	 * @param string $app
	 * @param string $user
	 * @param int $timestamp
	 * @param string $objectType
	 * @param int $objectId
	 * @param bool $expected
	 */
	public function testIsValidCommon($app, $user, $timestamp, $objectType, $objectId, $expected) {
		/** @var \OC\Notification\INotification|\PHPUnit_Framework_MockObject_MockObject $notification */
		$notification = $this->getMockBuilder('\OC\Notification\Notification')
			->setMethods([
				'getApp',
				'getUser',
				'getTimestamp',
				'getObjectType',
				'getObjectId',
			])
			->getMock();

		$notification->expects($this->any())
			->method('getApp')
			->willReturn($app);

		$notification->expects($this->any())
			->method('getUser')
			->willReturn($user);

		$notification->expects($this->any())
			->method('getTimestamp')
			->willReturn($timestamp);

		$notification->expects($this->any())
			->method('getObjectType')
			->willReturn($objectType);

		$notification->expects($this->any())
			->method('getObjectId')
			->willReturn($objectId);

		$this->assertEquals($expected, $this->invokePrivate($notification, 'isValidCommon'));
	}
}
