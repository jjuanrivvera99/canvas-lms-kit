<?php

namespace Tests\Api\Courses;

use GuzzleHttp\Psr7\Response;
use CanvasLMS\Http\HttpClient;
use PHPUnit\Framework\TestCase;
use CanvasLMS\Api\Courses\Course;
use CanvasLMS\Dto\Courses\CreateCourseDTO;
use CanvasLMS\Dto\Courses\UpdateCourseDTO;
use CanvasLMS\Exceptions\CanvasApiException;

class CourseTest extends TestCase
{
    /**
     * @var Course
     */
    private $course;

    /**
     * @var mixed
     */
    private $httpClientMock;

    /**
     * Set up the test
     */
    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(HttpClient::class);
        Course::setApiClient($this->httpClientMock);
        $this->course = new Course([]);
    }

    /**
     * Test the create course method
     * @return void
     */
    public function testCreateCourse(): void
    {
        $courseData = [
            'name' => 'Test Course',
            'courseCode' => 'TC101',
        ];

        $response = new Response(200, [], json_encode(['id' => 1, 'name' => 'Test Course']));
        
        $this->httpClientMock
            ->method('post')
            ->willReturn($response);

        $course = Course::create($courseData);
        
        $this->assertInstanceOf(Course::class, $course);
        $this->assertEquals('Test Course', $course->getName());
    }

    /**
     * Test the create course method with DTO
     * @return void
     */
    public function testCreateCourseWithDto(): void
    {
        $courseData = new CreateCourseDTO(['name' => 'Test Course', 'courseCode' => 'TC101']);

        $response = new Response(200, [], json_encode(['id' => 1, 'name' => 'Test Course']));
        
        $this->httpClientMock
            ->method('post')
            ->willReturn($response);

        $course = Course::create($courseData);
        
        $this->assertInstanceOf(Course::class, $course);
        $this->assertEquals('Test Course', $course->getName());
    }

    /**
     * Test the find course method
     * @return void
     */
    public function testFindCourse(): void
    {
        $response = new Response(200, [], json_encode(['id' => 123, 'name' => 'Found Course']));
        
        $this->httpClientMock
            ->method('get')
            ->willReturn($response);

        $course = Course::find(123);
        
        $this->assertInstanceOf(Course::class, $course);
        $this->assertEquals(123, $course->getId());
    }

    /**
     * Test the update course method
     * @return void
     */
    public function testUpdateCourse(): void
    {
        $courseData = [
            'name' => 'Updated Course',
        ];

        $response = new Response(200, [], json_encode(['id' => 1, 'name' => 'Updated Course']));
        
        $this->httpClientMock
            ->method('put')
            ->willReturn($response);

        $course = Course::update(1, $courseData);
        
        $this->assertEquals('Updated Course', $course->getName());
    }

    /**
     * Test the update course method with DTO
     * @return void
     */
    public function testUpdateCourseWithDto(): void
    {
        $courseData = new UpdateCourseDTO(['name' => 'Updated Course']);

        $response = new Response(200, [], json_encode(['id' => 1, 'name' => 'Updated Course']));
        
        $this->httpClientMock
            ->expects($this->once())
            ->method('put')
            ->willReturn($response);

        $course = Course::update(1, $courseData);
        
        $this->assertEquals('Updated Course', $course->getName());
    }

    /**
     * Test the save course method
     * @return void
     */
    public function testSaveCourseReturnsFalseOnException(): void
    {
        $this->course->setId(1);
        $this->course->setName('Test Course');

        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->will($this->throwException(new CanvasApiException()));

        $this->assertFalse($this->course->save());
    }

    /**
     * Test the delete course method
     * @return void
     */
    public function testDeleteCourse(): void
    {
        $response = new Response(200, [], json_encode(['id' => 123, 'name' => 'Found Course']));
        
        $this->httpClientMock
            ->method('get')
            ->willReturn($response);

        $course = Course::find(123);

        $response = new Response(200, [], json_encode(['deleted' => true]));
        
        $this->httpClientMock
            ->expects($this->once())
            ->method('delete')
            ->willReturn($response);

        $this->assertTrue($course->delete());
    }


    /**
     * Test the conclude course method
     * @return void
     */
    public function testConcludeCourse(): void
    {
        $response = new Response(200, [], json_encode(['id' => 123, 'name' => 'Found Course']));
        
        $this->httpClientMock
            ->method('get')
            ->willReturn($response);

        $course = Course::find(123);

        $response = new Response(200, [], json_encode(['conclude' => true]));
        
        $this->httpClientMock
            ->expects($this->once())
            ->method('delete')
            ->willReturn($response);

        $this->assertTrue($course->conclude());
    }


    /**
     * Test the reset course method
     * @return void
     */
    public function testResetCourse(): void
    {
        $response = new Response(200, [], json_encode(['id' => 123, 'name' => 'Found Course']));
        
        $this->httpClientMock
            ->method('get')
            ->willReturn($response);

        $course = Course::find(123);

        $response = new Response(200, [], json_encode(['id' => 123, 'name' => 'Reset Course']));
        
        $this->httpClientMock
            ->expects($this->once())
            ->method('post')
            ->willReturn($response);

        $resetCourse = $course->reset();
        
        $this->assertInstanceOf(Course::class, $resetCourse);
        $this->assertEquals('Reset Course', $resetCourse->getName());
    }

    protected function tearDown(): void
    {
        $this->course = null;
        $this->httpClientMock = null;
    }
}
