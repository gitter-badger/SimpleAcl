<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;

use SimpleAcl\Acl;
use SimpleAcl\Role;
use SimpleAcl\Resource;
use SimpleAcl\Rule;
use SimpleAcl\Role\RoleAggregate;
use SimpleAcl\Resource\ResourceAggregate;

class AclTest extends PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $acl = new Acl;

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
    }

    public function testUnDefinedRule()
    {
        $acl = new Acl;
        $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), true);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'UnDefinedRule'));
    }

    public function testUnDefinedRoleOrResource()
    {
        $acl = new Acl;
        $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), true);

        $this->assertFalse($acl->isAllowed('NotDefinedRole', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'NotDefinedResource', 'View'));
        $this->assertFalse($acl->isAllowed('NotDefinedRole', 'NotDefinedResource', 'View'));
    }

    public function testOneRoleOneResourceOneRule()
    {
        $acl = new Acl;
        $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), true);
        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));

        $acl = new Acl;
        $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), false);
        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
    }

    public function testOneRoleOneResourceMultipleRule()
    {
        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Page');

        $acl->addRule($user, $resource, new Rule('View'), true);
        $acl->addRule($user, $resource, new Rule('Edit'), true);
        $acl->addRule($user, $resource, new Rule('Remove'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Remove'));

        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Page');

        $acl->addRule($user, $resource, new Rule('View'), false);
        $acl->addRule($user, $resource, new Rule('Edit'), false);
        $acl->addRule($user, $resource, new Rule('Remove'), false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Remove'));
    }

    public function testMultipleRolesMultipleResourcesMultipleRules()
    {
        $runChecks = function(PHPUnit_Framework_TestCase $phpUnit, Acl $acl, $allowed) {
            // Checks for page
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Page', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Page', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Page', 'Remove'));

            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Page', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Page', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Page', 'Remove'));

            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Page', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Page', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Page', 'Remove'));
    
            // Checks for blog
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Blog', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Blog', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Blog', 'Remove'));
    
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Blog', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Blog', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Blog', 'Remove'));
    
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Blog', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Blog', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Blog', 'Remove'));
    
            // Checks for site
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Site', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Site', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('User', 'Site', 'Remove'));

            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Site', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Site', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Moderator', 'Site', 'Remove'));

            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Site', 'View'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Site', 'Edit'));
            $phpUnit->assertEquals($allowed, $acl->isAllowed('Admin', 'Site', 'Remove'));
        };
        
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $runChecks($this, $acl, false);

        // Rules for page
        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($user, $page, new Rule('Edit'), true);
        $acl->addRule($user, $page, new Rule('Remove'), true);

        $acl->addRule($moderator, $page, new Rule('View'), true);
        $acl->addRule($moderator, $page, new Rule('Edit'), true);
        $acl->addRule($moderator, $page, new Rule('Remove'), true);

        $acl->addRule($admin, $page, new Rule('View'), true);
        $acl->addRule($admin, $page, new Rule('Edit'), true);
        $acl->addRule($admin, $page, new Rule('Remove'), true);

        // Rules for blog
        $acl->addRule($user, $blog, new Rule('View'), true);
        $acl->addRule($user, $blog, new Rule('Edit'), true);
        $acl->addRule($user, $blog, new Rule('Remove'), true);

        $acl->addRule($moderator, $blog, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($moderator, $blog, new Rule('Remove'), true);

        $acl->addRule($admin, $blog, new Rule('View'), true);
        $acl->addRule($admin, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $blog, new Rule('Remove'), true);

        // Rules for site
        $acl->addRule($user, $site, new Rule('View'), true);
        $acl->addRule($user, $site, new Rule('Edit'), true);
        $acl->addRule($user, $site, new Rule('Remove'), true);

        $acl->addRule($moderator, $site, new Rule('View'), true);
        $acl->addRule($moderator, $site, new Rule('Edit'), true);
        $acl->addRule($moderator, $site, new Rule('Remove'), true);

        $acl->addRule($admin, $site, new Rule('View'), true);
        $acl->addRule($admin, $site, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $runChecks($this, $acl, true);

    }

    public function testParentRoles()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $admin->addChild($moderator);
        $moderator->addChild($user);

        $page = new Resource('Page');

        // Parent elements must NOT grant access
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));

        $acl = new Acl;

        // Child elements must inherit access
        $acl->addRule($admin, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));

        // but last added rules wins
        $acl->addRule($user, $page, new Rule('View'), false);
        $acl->addRule($moderator, $page, new Rule('View'), false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
    }

    public function testParentResources()
    {
        $acl = new Acl;

        $user = new Role('User');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $site->addChild($blog);
        $blog->addChild($page);

        // Parent elements must NOT have access
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));

        $acl = new Acl;

        // Child elements must inherit access
        $acl->addRule($user, $site, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));

        // but last added rules wins
        $acl->addRule($user, $page, new Rule('View'), false);
        $acl->addRule($user, $blog, new Rule('View'), false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));
    }

    public function testParentRolesAndResources()
    {
        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $admin->addChild($moderator);
        $moderator->addChild($user);

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $site->addChild($blog);
        $blog->addChild($page);

        $acl = new Acl;

        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));

        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));

        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

        $acl = new Acl;

        $acl->addRule($admin, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));

        $acl = new Acl;

        $acl->addRule($user, $site, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));

        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));

        // test add rule in the middle
        $acl = new Acl;

        $acl->addRule($moderator, $blog, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));

        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

        // test add rule on the top
        $acl = new Acl;

        $acl->addRule($admin, $site, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('Admin', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Site', 'View'));
    }

    public function testAggregateBadRolesAndResources()
    {
        $acl = new Acl;

       $user = new Role('User');

       $page = new Resource('Page');

       $acl->addRule($user, $page, new Rule('View'), true);

       $this->assertFalse($acl->isAllowed('User', new \stdClass(), 'View'));
       $this->assertFalse($acl->isAllowed(new \stdClass(), 'Page', 'Edit'));
    }

    public function testAggregateEmptyRolesAndResources()
    {
        $acl = new Acl;

       $user = new Role('User');
       $moderator = new Role('Moderator');
       $admin = new Role('Admin');

       $page = new Resource('Page');
       $blog = new Resource('Blog');
       $site = new Resource('Site');

       $userGroup = new RoleAggregate();
       $siteGroup = new ResourceAggregate();

       $acl->addRule($user, $page, new Rule('View'), true);
       $acl->addRule($moderator, $blog, new Rule('Edit'), true);
       $acl->addRule($admin, $site, new Rule('Remove'), true);

       $this->assertFalse($acl->isAllowed($userGroup, $siteGroup, 'View'));
       $this->assertFalse($acl->isAllowed($userGroup, $siteGroup, 'Edit'));
       $this->assertFalse($acl->isAllowed($userGroup, $siteGroup, 'Remove'));
    }

    public function testAggregateRoles()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $userGroup = new RoleAggregate();

        $userGroup->addRole($user);
        $userGroup->addRole($moderator);
        $userGroup->addRole($admin);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $this->assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));
        $this->assertTrue($acl->isAllowed($userGroup, 'Blog', 'Edit'));
        $this->assertTrue($acl->isAllowed($userGroup, 'Site', 'Remove'));
    }

    public function testAggregateResources()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $siteGroup = new ResourceAggregate();

        $siteGroup->addResource($page);
        $siteGroup->addResource($blog);
        $siteGroup->addResource($site);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $this->assertTrue($acl->isAllowed('User', $siteGroup, 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', $siteGroup, 'Edit'));
        $this->assertTrue($acl->isAllowed('Admin', $siteGroup, 'Remove'));
    }

    public function testAggregateRolesAndResources()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $userGroup = new RoleAggregate();
        $userGroup->addRole($user);
        $userGroup->addRole($moderator);
        $userGroup->addRole($admin);

        $siteGroup = new ResourceAggregate();
        $siteGroup->addResource($page);
        $siteGroup->addResource($blog);
        $siteGroup->addResource($site);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $blog, new Rule('Edit'), true);
        $acl->addRule($admin, $site, new Rule('Remove'), true);

        $this->assertTrue($acl->isAllowed($userGroup, $siteGroup, 'View'));
        $this->assertTrue($acl->isAllowed($userGroup, $siteGroup, 'Edit'));
        $this->assertTrue($acl->isAllowed($userGroup, $siteGroup, 'Remove'));
    }

    public function testStringAsRule()
    {
        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Page');

        $acl->addRule($user, $resource, 'View', true);
        $acl->addRule($user, $resource, 'Edit', true);
        $acl->addRule($user, $resource, 'Remove', true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Remove'));

        $acl = new Acl;

        $acl->setRuleClass('SimpleAcl\Rule');

        $user = new Role('User');
        $resource = new Resource('Page');

        $acl->addRule($user, $resource, 'View', false);
        $acl->addRule($user, $resource, 'Edit', false);
        $acl->addRule($user, $resource, 'Remove', false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Remove'));
    }

    public function testGetResult()
    {
        $self = $this;

        $testReturnResult = function ($result, $expected) use ($self) {
            $index = 0;
            foreach ($result as $r) {
                $self->assertSame($expected[$index], $r->getRule());
                $index++;
            }
            $self->assertEquals(count($expected), $index);
        };

        $acl = new Acl;

        $user = new Role('User');
        $resource = new Resource('Page');

        $view = new Rule('View');
        $edit = new Rule('Edit');
        $remove = new Rule('Remove');

        $acl->addRule($user, $resource, $view, true);
        $acl->addRule($user, $resource, $edit, true);
        $acl->addRule($user, $resource, $remove, true);

        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertTrue($acl->isAllowed('User', 'Page', 'Remove'));

        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'View'), array($view));
        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Edit'), array($edit));
        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Remove'), array($remove));

        $acl = new Acl;

        $acl->addRule($user, $resource, $view, false);
        $acl->addRule($user, $resource, $edit, false);
        $acl->addRule($user, $resource, $remove, false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
        $this->assertFalse($acl->isAllowed('User', 'Page', 'Remove'));

        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'View'), array($view));
        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Edit'), array($edit));
        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Remove'), array($remove));

        // test RuleResult order
        $acl = new Acl;

        $view1 = new Rule('View');
        $view2 = new Rule('View');
        $view3 = new Rule('View');
        $view4 = new Rule('View');

        $acl->addRule($user, $resource, $view, false);
        $acl->addRule($user, $resource, $view1, true);
        $acl->addRule($user, $resource, $view2, false);
        $acl->addRule($user, $resource, $view3, true);
        $acl->addRule($user, $resource, $view4, false);

        $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'View'), array($view4, $view3, $view2, $view1, $view));
    }


    /**
     * Testing edge conditions.
     */

    public function testEdgeConditionLastAddedRuleWins()
    {
        $acl = new Acl;

        $user = new Role('User');

        $page = new Resource('Page');

        $acl->addRule($user, $page, new Rule('View'), false);
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertAttributeCount(2, 'rules', $acl);
        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));

        $acl->removeRule(null, null, 'View', false);

        $this->assertAttributeCount(1, 'rules', $acl);
        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));

        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertAttributeCount(2, 'rules', $acl);
        $this->assertTrue($acl->isAllowed('User', 'Page', 'View'));

        $acl->addRule($user, $page, new Rule('View'), false);
        $this->assertAttributeCount(3, 'rules', $acl);
        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
    }

    public function testParentRolesAndResourcesWithMultipleRules()
    {
        $user = new Role('User');
        $moderator = new Role('Moderator');
        $admin = new Role('Admin');

        $admin->addChild($moderator);
        $moderator->addChild($user);

        $page = new Resource('Page');
        $blog = new Resource('Blog');
        $site = new Resource('Site');

        $site->addChild($blog);
        $blog->addChild($page);

        $acl = new Acl;

        $acl->addRule($moderator, $blog, new Rule('View'), true);
        $acl->addRule($user, $page, new Rule('View'), false);

        $this->assertFalse($acl->isAllowed('User', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('User', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('User', 'Site', 'View'));

        $this->assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
        $this->assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));

        $this->assertFalse($acl->isAllowed('Admin', 'Page', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));
        $this->assertFalse($acl->isAllowed('Admin', 'Site', 'View'));
    }


    public function testEdgeConditionAggregateRolesFirstAddedRoleWins()
    {
        $acl = new Acl;

        $user = new Role('User');
        $moderator = new Role('Moderator');

        $page = new Resource('Page');

        $userGroup = new RoleAggregate();

        $userGroup->addRole($user);
        $userGroup->addRole($moderator);

        $acl->addRule($user, $page, new Rule('View'), true);
        $acl->addRule($moderator, $page, new Rule('View'), false);

        $this->assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

        $userGroup->removeRole('User');
        $this->assertFalse($acl->isAllowed($userGroup, 'Page', 'View'));
        $userGroup->addRole($user);
        $this->assertFalse($acl->isAllowed($userGroup, 'Page', 'View'));

        $acl = new Acl;

        $userGroup = new RoleAggregate();

        $userGroup->addRole($user);
        $userGroup->addRole($moderator);

        // changing rule orders don't change result
        $acl->addRule($moderator, $page, new Rule('View'), false);
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

        $userGroup->removeRole('User');
        $this->assertFalse($acl->isAllowed($userGroup, 'Page', 'View'));
        $userGroup->addRole($user);
        $this->assertFalse($acl->isAllowed($userGroup, 'Page', 'View'));

        // test case when priority matter
        $acl = new Acl;

        $userGroup = new RoleAggregate();

        $userGroup->addRole($user);
        $userGroup->addRole($moderator);

        $contact = new Resource('Contact');
        $page->addChild($contact);

        $acl->addRule($moderator, $contact, new Rule('View'), true);
        $acl->addRule($user, $page, new Rule('View'), false);

        // user rule match first but moderator has higher priority
        $this->assertTrue($acl->isAllowed($userGroup, 'Contact', 'View'));

        $acl->addRule($user, $contact, new Rule('View'), false);

        // now priorities are equal
        $this->assertFalse($acl->isAllowed($userGroup, 'Contact', 'View'));
    }

    public function testEdgeConditionAggregateResourcesFirstAddedResourceWins()
    {
        $acl = new Acl;

        $user = new Role('User');

        $page = new Resource('Page');
        $blog = new Resource('Blog');

        $siteGroup = new ResourceAggregate();
        $siteGroup->addResource($page);
        $siteGroup->addResource($blog);

        $acl->addRule($user, $blog, new Rule('View'), false);
        $acl->addRule($user, $page, new Rule('View'), true);

        $this->assertTrue($acl->isAllowed('User', $siteGroup, 'View'));

        $siteGroup->removeResource('Page');

        $this->assertFalse($acl->isAllowed('User', $siteGroup, 'View'));
    }

    public function testComplexGraph()
    {
        $acl = new Acl();

        $u = new Role('U');
        $u1 = new Role('U1');
        $u2 = new Role('U2');
        $u3 = new Role('U3');

        $u->addChild($u1);
        $u->addChild($u2);
        $u->addChild($u3);

        $r = new Resource('R');
        $r1 = new Resource('R1');
        $r2 = new Resource('R2');
        $r3 = new Resource('R3');
        $r4 = new Resource('R4');
        $r5 = new Resource('R5');

        $r->addChild($r1);
        $r->addChild($r2);
        $r->addChild($r3);

        $r3->addChild($r4);
        $r3->addChild($r5);

        $a = new Rule('View');

        $acl->addRule($u, $r, $a, true);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R2', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R3', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R4', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R2', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R3', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R4', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R2', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R3', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R4', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R2', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R3', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R4', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R5', 'View'));

        $a2 = new Rule('View');

        $acl->addRule($u, $r3, $a2, false);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U2', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U2', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U2', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R5', 'View'));

        $a3 = new Rule('View');
        $a4 = new Rule('View');
        $acl->addRule($u2, $r4, $a3, true);
        $acl->addRule($u2, $r5, $a4, true);

        $this->assertTrue($acl->isAllowed('U', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U1', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U1', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U2', 'R3', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R4', 'View'));
        $this->assertTrue($acl->isAllowed('U2', 'R5', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R1', 'View'));
        $this->assertTrue($acl->isAllowed('U3', 'R2', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R3', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R4', 'View'));
        $this->assertFalse($acl->isAllowed('U3', 'R5', 'View'));
    }
}